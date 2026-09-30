<?php
/**
 * debug-items.php — READ-ONLY items diagnostics.
 *
 * Dumps every item with its category, variations (grouped by attribute), extras, addons,
 * offers and images, plus a list of detected problems. Nothing is written to the database.
 *
 * Local:  php debug-items.php            (run from the project root)
 * Server: upload to public/debug-items.php, open /debug-items.php?token=<TOKEN>
 *         DELETE IT FROM THE SERVER WHEN DONE.
 *
 * Optional: &item=258 to only show one item (issues/summary still cover everything).
 */

const DEBUG_TOKEN = '404dbfb0536911b69eb4795c876205ef5bf082f5';

$isCli = PHP_SAPI === 'cli';
if (!$isCli && !hash_equals(DEBUG_TOKEN, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit;
}

// Works from the project root (local) or from public/ (server).
$base = is_file(__DIR__ . '/artisan') ? __DIR__ : dirname(__DIR__);

require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

const ACTIVE   = 5;
const INACTIVE = 10;

$statusName = static fn($s) => (int) $s === ACTIVE ? 'active' : ((int) $s === INACTIVE ? 'inactive' : "unknown({$s})");
$onlyItem   = isset($_GET['item']) ? (int) $_GET['item'] : null;
$now        = date('Y-m-d H:i:s');

$issues = [];
$issue  = static function (string $type, string $message, array $context = []) use (&$issues) {
    $issues[] = ['type' => $type, 'message' => $message] + ($context ? ['context' => $context] : []);
};

/* ------------------------------------------------------------------ load */

$items      = DB::table('items')->orderBy('id')->get();                       // includes soft-deleted
$categories = DB::table('item_categories')->get()->keyBy('id');
$attributes = DB::table('item_attributes')->get()->keyBy('id');
$taxes      = DB::table('taxes')->get()->keyBy('id');
$variations = DB::table('item_variations')->whereNull('deleted_at')->get()->groupBy('item_id');
$extras     = DB::table('item_extras')->whereNull('deleted_at')->get()->groupBy('item_id');
$addons     = DB::table('item_addons')->whereNull('deleted_at')->get()->groupBy('item_id');
$offers     = DB::table('offers')->get()->keyBy('id');
$offerItems = DB::table('offer_items')->get()->groupBy('item_id');
$media      = DB::table('media')->where('model_type', 'App\\Models\\Item')->get()->groupBy('model_id');

$itemsById   = $items->keyBy('id');
$liveItems   = $items->whereNull('deleted_at');
$deletedIds  = $items->whereNotNull('deleted_at')->pluck('id')->all();

/* ------------------------------------------------------------ per item */

$report = [];

foreach ($liveItems as $item) {
    $category = $categories[$item->item_category_id] ?? null;
    $ctx      = ['item_id' => $item->id, 'item' => $item->name];

    if (!$category) {
        $issue('category_missing', 'Item points to a category that does not exist', $ctx + ['item_category_id' => $item->item_category_id]);
    } elseif ((int) $item->status === ACTIVE && (int) $category->status !== ACTIVE) {
        $issue('category_inactive', 'Active item is in an inactive category (hidden from menu/POS)', $ctx + ['category' => $category->name]);
    }

    if ((float) $item->price <= 0) {
        $issue('zero_price', 'Item base price is 0 or less', $ctx + ['price' => (float) $item->price]);
    }

    if ($item->tax_id !== null && !isset($taxes[$item->tax_id])) {
        $issue('tax_missing', 'Item points to a tax that does not exist', $ctx + ['tax_id' => $item->tax_id]);
    }

    // Variations grouped by attribute
    $variationGroups = [];
    foreach ($variations[$item->id] ?? [] as $v) {
        $attr = $attributes[$v->item_attribute_id] ?? null;
        $key  = $attr ? $attr->name : "missing_attribute#{$v->item_attribute_id}";

        if (!$attr) {
            $issue('variation_attribute_missing', 'Variation uses an attribute that no longer exists', $ctx + ['variation' => $v->name]);
        } elseif ((int) $attr->status !== ACTIVE) {
            $issue('variation_attribute_inactive', 'Variation uses an INACTIVE attribute', $ctx + ['variation' => $v->name, 'attribute' => $attr->name]);
        }
        if ((int) $v->status !== ACTIVE) {
            $issue('variation_inactive', 'Variation is inactive', $ctx + ['variation' => $v->name]);
        }

        $variationGroups[$key][] = [
            'id'     => $v->id,
            'name'   => $v->name,
            'price'  => (float) $v->price,
            'status' => $statusName($v->status),
        ];
    }

    foreach ($variationGroups as $attrName => $group) {
        $names = array_map(static fn($g) => mb_strtolower(trim($g['name'])), $group);
        if (count($names) !== count(array_unique($names))) {
            $issue('variation_duplicate', 'Same variation name appears twice under one attribute', $ctx + ['attribute' => $attrName]);
        }
    }

    // Extras
    $extraList = [];
    foreach ($extras[$item->id] ?? [] as $e) {
        if ((int) $e->status !== ACTIVE) {
            $issue('extra_inactive', 'Extra is inactive', $ctx + ['extra' => $e->name]);
        }
        $extraList[] = ['id' => $e->id, 'name' => $e->name, 'price' => (float) $e->price, 'status' => $statusName($e->status)];
    }

    // Addons
    $addonList = [];
    foreach ($addons[$item->id] ?? [] as $a) {
        $addonItem = $itemsById[$a->addon_item_id] ?? null;
        if (!$addonItem) {
            $issue('addon_missing', 'Addon points to an item that does not exist', $ctx + ['addon_item_id' => $a->addon_item_id]);
        } elseif ($addonItem->deleted_at !== null) {
            $issue('addon_deleted', 'Addon points to a DELETED item', $ctx + ['addon' => $addonItem->name]);
        } elseif ((int) $addonItem->status !== ACTIVE) {
            $issue('addon_inactive', 'Addon points to an inactive item', $ctx + ['addon' => $addonItem->name]);
        }
        if ((int) $a->addon_item_id === (int) $item->id) {
            $issue('addon_self', 'Item is set as an addon of itself', $ctx);
        }
        $addonList[] = [
            'addon_item_id' => $a->addon_item_id,
            'name'          => $addonItem->name ?? null,
            'price'         => $addonItem ? (float) $addonItem->price : null,
            'variation'     => $a->addon_item_variation ? json_decode($a->addon_item_variation, true) : null,
        ];
    }

    // Offers
    $offerList   = [];
    $activeCount = 0;
    foreach ($offerItems[$item->id] ?? [] as $oi) {
        $offer = $offers[$oi->offer_id] ?? null;
        if (!$offer) {
            continue;
        }
        $running = (int) $offer->status === ACTIVE && $offer->start_date <= $now && $offer->end_date >= $now;
        $activeCount += $running ? 1 : 0;
        $offerList[] = [
            'name'    => $offer->name,
            'amount'  => (float) $offer->amount,
            'status'  => $statusName($offer->status),
            'start'   => $offer->start_date,
            'end'     => $offer->end_date,
            'running' => $running,
        ];
    }
    if ($activeCount > 1) {
        $issue('offer_multiple', 'Item is in more than one running offer', $ctx + ['count' => $activeCount]);
    }

    // Images
    $images = [];
    foreach ($media[$item->id] ?? [] as $m) {
        $path   = $m->id . '/' . $m->file_name;
        $exists = null;
        try {
            $exists = Storage::disk($m->disk)->exists($path);
        } catch (Throwable $t) {
            // unknown disk — leave as null
        }
        $images[] = ['collection' => $m->collection_name, 'file' => $path, 'disk' => $m->disk, 'exists' => $exists];
    }
    $itemImages = array_filter($images, static fn($i) => $i['collection'] === 'item');
    if (!$itemImages) {
        $issue('image_missing', 'Item has no image', $ctx);
    } elseif (end($itemImages)['exists'] === false) {
        $issue('image_file_missing', 'Item image record exists but the file is missing on disk', $ctx + ['file' => end($itemImages)['file']]);
    }

    if ($onlyItem === null || $onlyItem === (int) $item->id) {
        $report[] = [
            'id'         => $item->id,
            'name'       => $item->name,
            'slug'       => $item->slug,
            'category'   => $category->name ?? null,
            'price'      => (float) $item->price,
            'status'     => $statusName($item->status),
            'item_type'  => (int) $item->item_type === 5 ? 'veg' : 'non-veg',
            'featured'   => (int) $item->is_featured === 5,
            'tax'        => $item->tax_id ? ($taxes[$item->tax_id]->name ?? 'MISSING') : null,
            'variations' => $variationGroups,
            'extras'     => $extraList,
            'addons'     => $addonList,
            'offers'     => $offerList,
            'images'     => $images,
        ];
    }
}

/* ------------------------------------------------------- global checks */

foreach ($liveItems->groupBy(static fn($i) => mb_strtolower(trim($i->name))) as $name => $group) {
    if ($group->count() > 1) {
        $issue('duplicate_name', 'Several items share the same name', ['name' => $name, 'ids' => $group->pluck('id')->all()]);
    }
}
foreach ($liveItems->groupBy('slug') as $slug => $group) {
    if ($group->count() > 1) {
        $issue('duplicate_slug', 'Several items share the same slug (item page links may open the wrong item)', ['slug' => $slug, 'ids' => $group->pluck('id')->all()]);
    }
}
foreach ($categories as $category) {
    if (!$liveItems->where('item_category_id', $category->id)->count()) {
        $issue('category_empty', 'Category has no items', ['category' => $category->name, 'status' => $statusName($category->status)]);
    }
}
foreach ($attributes as $attr) {
    $used = DB::table('item_variations')->whereNull('deleted_at')->where('item_attribute_id', $attr->id)->count();
    if ($used === 0) {
        $issue('attribute_unused', 'Attribute is not used by any variation', ['attribute' => $attr->name]);
    }
}

/* ------------------------------------------------------------- summary */

$summary = [
    'generated_at'        => $now,
    'items_total'         => $liveItems->count(),
    'items_active'        => $liveItems->where('status', ACTIVE)->count(),
    'items_inactive'      => $liveItems->where('status', INACTIVE)->count(),
    'items_soft_deleted'  => count($deletedIds),
    'categories'          => $categories->map(static fn($c) => [
        'name'   => $c->name,
        'status' => $statusName($c->status),
        'items'  => $liveItems->where('item_category_id', $c->id)->count(),
    ])->values(),
    'attributes'          => $attributes->map(static fn($a) => ['id' => $a->id, 'name' => $a->name, 'status' => $statusName($a->status)])->values(),
    'items_with_variations' => $liveItems->filter(static fn($i) => isset($variations[$i->id]))->count(),
    'items_with_extras'     => $liveItems->filter(static fn($i) => isset($extras[$i->id]))->count(),
    'items_with_addons'     => $liveItems->filter(static fn($i) => isset($addons[$i->id]))->count(),
    'price_min'           => (float) $liveItems->min('price'),
    'price_max'           => (float) $liveItems->max('price'),
    'issue_counts'        => collect($issues)->countBy('type'),
];

$output = json_encode(
    ['summary' => $summary, 'issues' => $issues, 'items' => $report],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

if (!$isCli) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
}
echo $output, "\n";

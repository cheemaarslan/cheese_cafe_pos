<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A physical stock count. Written only through InventoryCountService, which sets each counted item's stock to
 * what was found and records the difference.
 */
class InventoryCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

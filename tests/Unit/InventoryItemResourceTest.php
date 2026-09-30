<?php

namespace Tests\Unit;

use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class InventoryItemResourceTest extends TestCase
{
    public function test_it_marks_inventory_at_the_minimum_level_as_low_stock(): void
    {
        $item = new InventoryItem([
            'name' => 'Cold Drink',
            'quantity' => 10,
            'unit' => 'bottle',
            'minimum_stock' => 10,
            'unit_cost' => 80,
            'status' => 5,
        ]);

        $data = (new InventoryItemResource($item))->toArray(Request::create('/'));

        $this->assertTrue($data['is_low_stock']);
        $this->assertSame('800.000000', $data['total_value']);
    }

    public function test_it_marks_inventory_above_the_minimum_level_as_available(): void
    {
        $item = new InventoryItem([
            'name' => 'Bread',
            'quantity' => 20,
            'unit' => 'loaf',
            'minimum_stock' => 5,
            'status' => 5,
        ]);

        $data = (new InventoryItemResource($item))->toArray(Request::create('/'));

        $this->assertFalse($data['is_low_stock']);
        $this->assertNull($data['total_value']);
    }
}

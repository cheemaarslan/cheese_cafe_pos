<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            // Base unit of the item (g, ml, pcs): + stock in, - stock out
            'quantity' => $this->quantity,
            'balance_after' => $this->balance_after,
            'unit' => optional($this->inventoryItem)->unit ?? '',
            'order_id' => $this->order_id,
            'note' => $this->note ?? '',
            'created_by_name' => optional($this->creator)->name ?? '',
            'converted_created_at' => $this->created_at ? AppLibrary::datetime($this->created_at) : '',
        ];
    }
}

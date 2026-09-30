<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A POS order in the raw (unformatted) shape the POS screen needs to load it
 * back into the cart for editing.
 */
class PosOrderEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'order_serial_no'    => $this->order_serial_no,
            'token'              => $this->token,
            'customer_id'        => $this->user_id,
            'branch_id'          => $this->branch_id,
            'order_type'         => $this->order_type,
            'dining_table_id'    => $this->dining_table_id,
            'instruction'        => $this->instruction,
            'discount'           => (float) $this->discount,
            'delivery_charge'    => (float) $this->delivery_charge,
            'pos_payment_method' => $this->pos_payment_method,
            'status'             => $this->status,
            'address'            => $this->address?->address,
            'order_items'        => $this->orderItems->load('itemable', 'orderItem')->map(function ($orderItem) {
                $item = $orderItem->itemable ?? $orderItem->orderItem;

                return [
                    'item_id'              => $orderItem->item_id,
                    'is_custom'            => blank($orderItem->itemable_type) && blank($orderItem->item_id),
                    'name'                 => $item?->name ?? $orderItem->item_name,
                    'image'                => $item?->thumb ?? null,
                    'quantity'             => (int) $orderItem->quantity,
                    'discount'             => (float) $orderItem->discount,
                    'price'                => (float) $orderItem->price,
                    'instruction'          => $orderItem->instruction ?? '',
                    'item_variation_total' => (float) $orderItem->item_variation_total,
                    'item_extra_total'     => (float) $orderItem->item_extra_total,
                    'item_variations'      => json_decode($orderItem->item_variations) ?? [],
                    'item_extras'          => json_decode($orderItem->item_extras) ?? [],
                ];
            })->values(),
        ];
    }
}

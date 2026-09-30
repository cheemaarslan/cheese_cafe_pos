<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // Handle both array (from settings) and model
        $data = is_array($this->resource) ? $this->resource : [
            'id'         => $this->id ?? null,
            'percentage' => $this->percentage ?? null,
            'min_order_price' => $this->min_order_price ?? null,
            'status'     => $this->status ?? null,
        ];
        return [
            'id'         => $data['id'] ?? null,
            'percentage' => $data['percentage'] ?? null,
            'status'     => $data['status'] ?? null,
        ];
    }
}

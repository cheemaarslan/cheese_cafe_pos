<?php

namespace App\Http\Resources;
use App\Enums\Status;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Libraries\AppLibrary;

class CustomerLoyaltyPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'customer_id'    => $this->customer_id,
            'order_id'       => $this->order->order_serial_no ?? null,
            'order_subtotal' => AppLibrary::currencyAmountFormat($this->order_subtotal),
            'percentage'     => (float) $this->percentage,
            'points'         => (int) $this->points,
            'status'         => $this->status,
            'status_text'    => $this->status === Status::ACTIVE ? 'Active' : 'Inactive',
            'earned_at'      => AppLibrary::datetime($this->earned_at),
            'expired_at'     => AppLibrary::datetime($this->expired_at),
            'created_at'     => AppLibrary::datetime($this->created_at),
            'updated_at'     => AppLibrary::datetime($this->updated_at),
        ];
    }
}

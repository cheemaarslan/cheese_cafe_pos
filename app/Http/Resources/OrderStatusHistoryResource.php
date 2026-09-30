<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
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
            'id'                => $this->id,
            'from_status'       => $this->from_status,
            'from_status_name'  => $this->from_status !== null ? trans('orderStatus.' . $this->from_status) : null,
            'to_status'         => $this->to_status,
            'to_status_name'    => trans('orderStatus.' . $this->to_status),
            'reason'            => $this->reason,
            'changed_by_id'     => $this->changed_by_id,
            'changed_by_name'   => $this->changed_by_name,
            'changed_by_source' => $this->changed_by_source,
            'changed_at'        => AppLibrary::datetime($this->created_at),
        ];
    }
}

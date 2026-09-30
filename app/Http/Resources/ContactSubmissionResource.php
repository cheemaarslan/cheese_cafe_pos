<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactSubmissionResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'message'     => $this->message,
            'date_time'   => AppLibrary::datetime($this->created_at),
            'create_date' => AppLibrary::date($this->created_at),
            'update_date' => AppLibrary::date($this->updated_at),
        ];
    }
}

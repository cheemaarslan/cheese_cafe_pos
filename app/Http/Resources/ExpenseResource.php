<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category ?? '',
            'amount' => $this->amount,
            'amount_currency_price' => AppLibrary::currencyAmountFormat($this->amount),
            'expense_date' => optional($this->expense_date)->format('Y-m-d'),
            'converted_expense_date' => $this->expense_date ? AppLibrary::date($this->expense_date) : '',
            'notes' => $this->notes ?? '',
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'amount' => 'decimal:6',
        'expense_date' => 'date',
    ];
}

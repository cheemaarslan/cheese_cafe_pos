<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'reason',
        'changed_by_id',
        'changed_by_name',
        'changed_by_source',
        'created_at',
    ];

    protected $casts = [
        'order_id'      => 'integer',
        'from_status'   => 'integer',
        'to_status'     => 'integer',
        'changed_by_id' => 'integer',
        'created_at'    => 'datetime',
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

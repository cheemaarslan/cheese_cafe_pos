<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminNotification extends Model
{
    use HasFactory;

    protected $table = 'admin_notifications';

    protected $fillable = [
        'admin_id',
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'admin_id' => 'integer',
        'type' => 'string',
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}

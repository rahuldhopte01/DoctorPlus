<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CannaleoSyncLog extends Model
{
    use HasFactory;

    protected $table = 'cannaleo_sync_log';

    protected $fillable = [
        'started_at',
        'completed_at',
        'status',
        'items_fetched',
        'pharmacies_created',
        'pharmacies_updated',
        'medicines_created',
        'medicines_updated',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}

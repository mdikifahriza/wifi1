<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
    protected $fillable = [
        'order_id',
        'name',
        'nim',
        'duration_seconds',
        'gross_amount',
        'status',
    ];

        /**
         * The attributes that should be cast.
         *
         *@var array
         */
    protected $casts = [
        'duration_seconds' => 'integer',
        'gross_amount' => 'integer',
    ];
}
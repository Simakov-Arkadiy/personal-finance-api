<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount', 'type', 'category', 'description'];

    protected $dateFormat = "Y-m-d\TH:i:s";

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }
}

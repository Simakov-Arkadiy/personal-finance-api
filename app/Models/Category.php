<?php

namespace App\Models;

use App\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    protected $dateFormat = "Y-m-d\TH:i:s";

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
        ];
    }
}

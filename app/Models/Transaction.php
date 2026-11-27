<?php

namespace App\Models;

use App\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['amount', 'type', 'category_id', 'description'];

    protected $dateFormat = "Y-m-d\TH:i:s";

    protected function casts(): array
    {
        return ['amount' => 'decimal:2',
            'type' => TransactionType::class];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

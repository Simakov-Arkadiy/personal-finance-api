<?php

namespace App\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function create(array $data): Transaction;

    public function getAll(): Collection;

    public function get(int $id): ?Transaction;

    public function update(int $id, array $data): Transaction;

    public function delete(int $id): ?Transaction;
}

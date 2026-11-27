<?php

namespace App\Contracts;

use App\Dto\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function create(TransactionDTO $data): Transaction;

    public function getAll(): Collection;

    public function get(int $id): Transaction;

    public function update(int $id, TransactionDTO $data): Transaction;

    public function delete(int $id): Transaction;
}

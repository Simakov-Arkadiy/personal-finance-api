<?php

namespace App\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function create(array $data): Category;

    public function getAll(): Collection;

    public function get(int $id): Category;

    public function update(int $id, array $data): Category;

    public function delete(int $id): bool;
}

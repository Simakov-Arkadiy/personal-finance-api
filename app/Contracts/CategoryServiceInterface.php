<?php

namespace App\Contracts;

use App\Dto\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    public function create(CategoryDTO $data): Category;

    public function getAll(): Collection;

    public function get(int $id): Category;

    public function update(int $id, CategoryDTO $data): Category;

    public function delete(int $id): bool;
}

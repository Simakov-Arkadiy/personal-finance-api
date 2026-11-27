<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Override;

class CategoryService implements CategoryServiceInterface
{
    #[Override]
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    #[Override]
    public function getAll(): Collection
    {
        return Category::all();
    }

    #[Override]
    public function get(int $id): Category
    {
        return Category::findOrFail($id);
    }

    #[Override]
    public function update(int $id, array $data): Category
    {
        $transaction = Category::findOrFail($id);

        $transaction->update($data);

        return $transaction;
    }

    #[Override]
    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);

        if ($category->transactions()->exists()) {
            return false;
        }
        $category->delete();

        return $category;
    }
}

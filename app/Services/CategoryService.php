<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Dto\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Override;

class CategoryService implements CategoryServiceInterface
{
    #[Override]
    public function create(CategoryDTO $data): Category
    {
        return Category::create([
            'name' => $data->name,
            'type' => $data->type,
        ]);
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
    public function update(int $id, CategoryDTO $data): Category
    {
        $category = Category::findOrFail($id);

        $category->update([
            'name' => $data->name,
            'type' => $data->type,
        ]);

        return $category;
    }

    #[Override]
    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);

        if ($category->transactions()->exists()) {
            return false;
        }

        return $category->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categoryes = Category::all();

        return response()->json(['data' => $categoryes, 'message' => 'Your categoryes!']);
    }

    public function show(int $id): JsonResponse
    {
        $categoryes = Category::find($id);
        if (! $categoryes) {
            return response()->json([['message' => 'Category not found.'], Response::HTTP_NOT_FOUND]);
        }

        return response()->json(['data' => $categoryes]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json(['data' => $category, 'message' => 'Categoryn created succefully'], Response::HTTP_CREATED);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = Category::find($id);
        if (! $category) {
            return response()->json(['message' => 'Category not found', Response::HTTP_NOT_FOUND]);
        }

        $category->update($request->validated());

        return response()->json(['data' => $category, 'message' => 'Category updated successfully']);
    }

    public function delete(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found', Response::HTTP_NOT_FOUND]);
        }
        if ($category->transactions()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with associated transactions',
            ], Response::HTTP_CONFLICT);
        }
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}

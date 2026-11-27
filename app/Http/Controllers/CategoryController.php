<?php

namespace App\Http\Controllers;

use App\Contracts\CategoryServiceInterface;
use App\Dto\CategoryDTO;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryServiceInterface $categoryService
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return response()->json(['data' => $categories, 'message' => 'Your categoryes!']);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->get($id);

        return response()->json(['data' => $category]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create(CategoryDTO::from($request->validated()));

        return response()->json(['data' => $category, 'message' => 'Category created succefully'], Response::HTTP_CREATED);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->update($id, CategoryDTO::from($request->validated()));

        return response()->json(['data' => $category, 'message' => 'Category updated successfully']);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->delete($id);

        if (! $category) {
            return response()->json([
                'message' => 'Cannot delete category with associated transactions',
            ], Response::HTTP_CONFLICT);
        }

        return response()->json(['message' => 'Category deleted successfully']);
    }
}

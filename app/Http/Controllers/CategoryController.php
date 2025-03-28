<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): JsonResponse
    {
        abort_if(Gate::denies('access-categories'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = $this->categoryRepository->getAllCategories();
        return response()->json(
            CategoryResource::collection($categories)
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        abort_if(Gate::denies('create-categories'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category = Category::create($request->validated() + ['user_id' => auth()->id()]);

        if ($request->hasFile('image')) {
            $category->addMediaFromRequest($request->input('image'))
                ->toMediaCollection('images');
        }

        return response()->json([
            new CategoryResource($category)
        ], Response::HTTP_CREATED);
    }

    public function show(string $slug): JsonResponse
    {
        abort_if(Gate::denies('access-categories'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category = $this->categoryRepository->getCategory($slug);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json(
            new CategoryResource($category)
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        
        abort_if(Gate::denies('edit-categories'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category->update($request->validated());

        if ($request->hasFile('image')) {
            $category->clearMediaCollection('images');
            $category->addMediaFromRequest($request->input('image'))
                ->toMediaCollection('images');
        }

        return response()->json(
            new CategoryResource($category->fresh())
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        abort_if(Gate::denies('delete-categories'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

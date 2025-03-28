<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index(): JsonResponse
    {
        abort_if(Gate::denies('access-posts'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $posts = $this->postRepository->getLatestPosts();
        return response()->json(
            PostResource::collection($posts)
        );
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        abort_if(Gate::denies('create-posts'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post = Post::create($request->validated() + ['user_id' => auth()->id()]);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')
                ->toMediaCollection('images');
        }

        return response()->json([
            new PostResource($post->load(['categories', 'author.media', 'media']))
        ], Response::HTTP_CREATED);
    }

    public function show(string $slug): JsonResponse
    {
        abort_if(Gate::denies('access-posts'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $post = $this->postRepository->getPostBySlug($slug);
        return response()->json(
            new PostResource($post->loadMissing(['categories', 'comments.user', 'author.media', 'media']))
        );
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        abort_if(Gate::denies('edit-posts'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $post->update($request->validated());
        if ($request->hasFile('image')) {
            $post->clearMediaCollection('images');
            $post->addMediaFromRequest('image')
                ->toMediaCollection('images');
        }

        return response()->json(
            new PostResource($post->fresh(['categories', 'author.media', 'media']))
        );
    }

    public function destroy(Post $post): JsonResponse
    {
        abort_if(Gate::denies('delete-posts'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    public function getRelatedPosts(){
        $posts = $this->postRepository->getRelatedPosts();
        return response()->json(PostResource::collection($posts));
    }

}

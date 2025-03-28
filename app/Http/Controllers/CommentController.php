<?php

namespace App\Http\Controllers;

use App\Events\CommentAddedEvent;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function index(): JsonResponse
    {
        abort_if(Gate::denies('access-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comments = $this->commentRepository->getAllComments();
        return response()->json(
            CommentResource::collection($comments)
        );
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        abort_if(Gate::denies('create-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment = $this->commentRepository->create(
            $request->validated() + ['user_id' => auth()->id()]
        );

        return response()->json(
            new CommentResource($comment->load('user.media')));
    }

    public function show(Comment $comment): JsonResponse
    {
        abort_if(Gate::denies('access-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json(
            new CommentResource($comment->loadMissing('user.media'))
        );
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        abort_if(Gate::denies('edit-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $comment = $this->commentRepository->update($request->validated(), $comment->id);
        return response()->json(
            new CommentResource($comment->fresh('user.media'))
        );
    }

    public function destroy(Comment $comment): JsonResponse
    {
        abort_if(Gate::denies('delete-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function confirm(Comment $comment): JsonResponse
    {
        abort_if(Gate::denies('confirm-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $comment = $this->commentRepository->confirm($comment->id);
        
        return response()->json(
            new CommentResource($comment)
        );
    }
    public function getRelatedComments()  {
        abort_if(Gate::denies('access-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $comments = $this->commentRepository->getRelatedComments();
        return response()->json(CommentResource::collection($comments));
    }
    public function getUnconfirmedComments()  {
        abort_if(Gate::denies('confirm-comments'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $comments = $this->commentRepository->getUnconfirmedComments();
        return response()->json(CommentResource::collection($comments));
    }
}

<?php

namespace App\Repositories;

use App\Events\CommentAddedEvent;
use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use App\Notifications\CommentConfirmedNotification;
use App\Notifications\NewCommentNotification;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    public function getCommentsByPost(int $postId): Collection
    {
        return Comment::with(['user.media'])
            ->where('post_id', $postId)
            ->latest()
            ->get();
    }

    public function create(array $data): Comment
    {
        $comment = Comment::create($data);
        if(auth()->user()->getIsAdminAtrribute()){
            $comment->confirmed_by = auth()->id();
            $comment->confirmed_at = now();
            $comment->save();
;
            broadcast(new CommentAddedEvent($comment));
            $comment->load('post.author')->post
                ->author->notify(new NewCommentNotification($comment));
        }
        return  $comment;
    }

    public function getAllComments(): Collection
    {
        return Comment::with(['user.media', 'confirmedBy'])
            ->latest()
            ->get();
    }

    public function update(array $data, int $id): Comment
    {
        $comment = Comment::findOrFail($id);
        $comment->update($data);
        return $comment->fresh(['user.media']);
    }

    public function confirm(int $id): Comment
    {
        $comment = Comment::findOrFail($id);
        $comment->update([
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id()
        ]);
        broadcast(new CommentAddedEvent($comment));
        $comment->load('post.author' )->post
            ->author->notify(new NewCommentNotification($comment));
        $comment->load( 'user')
            ->user->notify(new CommentConfirmedNotification($comment));
        return $comment->fresh(['user.media', 'confirmedBy']);
    }
    public function getRelatedComments(){
        $comments = Comment::where('user_id' , auth()->id())
            ->latest()
            ->withTrashed()
            ->get();
        return $comments->fresh(['user.media', 'confirmedBy']);
    }
    public function getUnconfirmedComments(){
        $comments = Comment::whereNull('confirmed_by')
            ->get();
        return $comments->fresh(['user.media', 'confirmedBy']);
    }

}

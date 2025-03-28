<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'slug' => $this->slug,
            'published_at' => $this->published_at,
            'image' => optional($this->getFirstMedia('images'))->getUrl(),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'last_name' => $this->author->last_name,
                'avatar' => optional($this->author->getFirstMedia('images'))->getUrl(),
            ],
            'categories' => $this->categories,
            'comments' => $this->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'post' => $comment->post,
                    'user' => [
                        'id'=>$comment->user->id,
                        'name' => $comment->user->name,
                        'last_name' => $comment->user->last_name,
                        'avatar' => optional($comment->user->getFirstMedia('images'))->getUrl(),
                    ],
                    'confirmed_by' => $comment->confirmedBy ? $comment->confirmedBy->name . ' ' . $comment->confirmedBy->last_name:'',
                    'confirmed_at' => $comment->confirmed_at,
                    'updated_at' => $comment->updated_at,
                    'created_at' => $comment->created_at,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

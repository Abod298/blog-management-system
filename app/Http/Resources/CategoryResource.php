<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => optional($this->getFirstMedia('images'))->getUrl(),
            'slug' => $this->slug,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'last_name' => $this->user->last_name,
                'avatar' => optional($this->user->getFirstMedia('images'))->getUrl(),
            ],
            'posts' => $this->posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'image' => optional($post->getFirstMedia('images'))->getUrl(),
                ];
            }),
        ];
    }
}

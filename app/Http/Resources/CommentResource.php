<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'post' => $this->post,
            'user' => [
                'id'=>$this->user->id,
                'name' => $this->user->name,
                'last_name' => $this->user->last_name,
                'avatar' => optional($this->user->getFirstMedia('images'))->getUrl(),
            ],
            'confirmed_by' => $this->confirmedBy ? $this->confirmedBy->name . ' ' . $this->confirmedBy->last_name:'',
            'confirmed_at' => $this->confirmed_at,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}

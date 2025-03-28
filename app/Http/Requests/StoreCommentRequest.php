<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create-comments');
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:1000',
            'post_id' => 'required|exists:posts,id',
        ];
    }
}

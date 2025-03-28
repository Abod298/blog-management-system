<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('edit-comments');
    }

    public function rules(): array
    {
        return [
            'body' => 'sometimes|required|string|max:1000',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'status' => 'required|in:0,1',
        ];
    }
    public function messages(): array
    {
        return [
            'post_id.required' => 'Post ID is required.',
            'post_id.exists' => 'The selected post does not exist.',
            'author_name.required' => 'Author name is required.',
            'author_name.string' => 'Author name must be a string.',
            'author_name.max' => 'Author name may not be greater than 255 characters.',
            'author_email.required' => 'Author email is required.',
            'author_email.email' => 'Author email must be a valid email address.',
            'author_email.max' => 'Author email may not be greater than 255 characters.',
            'content.required' => 'Comment content is required.',
            'content.string' => 'Comment content must be a string.',
            'parent_id.exists' => 'The selected parent comment does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either 0 (pending) or 1 (approved).',
        ];
    }
}

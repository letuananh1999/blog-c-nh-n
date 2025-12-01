<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'id'          => 'sometimes|exists:posts,id',
            'category_id' => 'required|exists:categories,id',
            'user_id'    => 'sometimes|exists:users,id',
            'title'       => 'required|string|max:255',
            'slug'        => 'sometimes|string|max:255|unique:posts,slug,' . $this->id,
            'excerpt'     => 'nullable|string',
            'content'     => 'required',
            'thumbnail'   => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status'      => 'required|in:draft,published,archived',
            'view_count'  => 'sometimes|biginteger|min:0',
            'like_count'  => 'sometimes|biginteger|min:0',
            'published_at' => 'nullable|date',
            // 'tags'        => 'array',
            // 'tags.*'      => 'exists:tags,id'
            'created_at'  => 'sometimes|date',
            'updated_at'  => 'sometimes|date',
        ];
    }
}

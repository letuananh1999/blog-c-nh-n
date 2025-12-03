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
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status'      => 'required|in:draft,published,archived',
            'view_count'  => 'sometimes|biginteger|min:0',
            'like_count'  => 'sometimes|biginteger|min:0',
            'published_at' => 'nullable|date',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
            'created_at'  => 'sometimes|date',
            'updated_at'  => 'sometimes|date',
        ];
    }

    /**
     * Custom validation messages in Vietnamese
     */
    public function messages(): array
    {
        return [
            'title.required'              => 'Tiêu đề bài viết không được để trống',
            'title.string'                => 'Tiêu đề phải là văn bản',
            'title.max'                   => 'Tiêu đề không được quá 255 ký tự',

            'excerpt.string'              => 'Mô tả ngắn phải là văn bản',

            'content.required'            => 'Nội dung bài viết không được để trống',

            'category_id.required'        => 'Vui lòng chọn danh mục',
            'category_id.exists'          => 'Danh mục được chọn không tồn tại',

            'thumbnail.image'             => 'File tải lên phải là hình ảnh',
            'thumbnail.mimes'             => 'Hình ảnh phải có định dạng: jpeg, png, gif, webp',
            'thumbnail.max'               => 'Kích thước hình ảnh không quá 2MB',

            'meta_title.string'           => 'Tiêu đề SEO phải là văn bản',
            'meta_title.max'              => 'Tiêu đề SEO không quá 255 ký tự',

            'meta_description.string'     => 'Mô tả SEO phải là văn bản',
            'meta_description.max'        => 'Mô tả SEO không quá 500 ký tự',

            'status.required'             => 'Trạng thái không được để trống',
            'status.in'                   => 'Trạng thái phải là: draft, published hoặc archived',

            'tags.array'                  => 'Tags phải là mảng',
            'tags.*.exists'               => 'Một hoặc nhiều tags không tồn tại',
        ];
    }

    /**
     * Custom attribute names for better error messages
     */
    public function attributes(): array
    {
        return [
            'title'               => 'Tiêu đề',
            'excerpt'             => 'Mô tả ngắn',
            'content'             => 'Nội dung',
            'category_id'         => 'Danh mục',
            'thumbnail'           => 'Hình ảnh đại diện',
            'meta_title'          => 'Tiêu đề SEO',
            'meta_description'    => 'Mô tả SEO',
            'status'              => 'Trạng thái',
            'tags'                => 'Tags',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho Category Update Request
 */
class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'version' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc',
            'version.required' => 'Không tìm thấy version. Vui lòng tải lại trang',
            'version.integer' => 'Dữ liệu không hợp lệ',
        ];
    }
}

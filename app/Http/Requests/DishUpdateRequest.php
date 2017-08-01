<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DishUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'exists:categories,id',
            'name' => 'max:255',
            'price' => 'numeric|between:0.01,200',
            'description' => 'max:1000',
            'isactive' => 'in:0,1',
            'image' => 'array',
            'image.*' => 'mimetypes:image/jpeg,image/bmp,image/png,image/gif,image/svg,video/avi,video/mpeg,video/quicktime,video/mp4',
        ];
    }
}

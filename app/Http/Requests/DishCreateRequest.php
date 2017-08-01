<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DishCreateRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric|between:0.01,200',
            'description' => 'required|max:1000',
            'isactive' => 'required|in:0,1',
            'image' => 'required|array',
            'image.*' => 'mimetypes:image/jpeg,image/bmp,image/png,image/gif,image/svg,video/avi,video/mpeg,video/quicktime,video/mp4',
        ];
    }
}

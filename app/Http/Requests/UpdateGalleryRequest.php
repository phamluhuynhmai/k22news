<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
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
            'title' => 'required|max:160|unique:galleries,title,'.$this->route('gallery_image'),
            'images.*' => 'required|mimes:jpeg,png,jpg,webp,svg',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'images.*.mimes' => __('messages.placeholder.the_images_must_be_a_file_of_type'),
        ];
    }
}

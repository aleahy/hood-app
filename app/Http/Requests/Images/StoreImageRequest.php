<?php

namespace App\Http\Requests\Images;

use App\Services\RetrieveImageService;
use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $extensions = RetrieveImageService::getMimeTypeCollection()
            ->map(fn($type) => '.' . $type)
            ->join(',');

        return [
            'image_uri' => [
                'required',
                'string',
                'max:255',
                'min:5',
                'ends_with:'. $extensions,
            ]
        ];
    }
}

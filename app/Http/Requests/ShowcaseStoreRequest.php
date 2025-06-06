<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowcaseStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'sometimes|string',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            // 'shelf_depth' => 'required|numeric',
            'shelves' => 'required|array',
            "shelves.*.distance"  => "required|numeric|min:15|max:200",
            "shelves.*.shelf_depth"  => "required|numeric|min:15|max:300",
            'comment' => 'nullable',
        ];
    }
}

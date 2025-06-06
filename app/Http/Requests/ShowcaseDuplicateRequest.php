<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowcaseDuplicateRequest extends FormRequest
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
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string',
            'description' => 'sometimes|string',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            // 'shelf_depth' => 'required|numeric',
            'shelves' => 'required|array',
            "shelves.*.distance"  => "required|numeric|min:20|max:70",
            "shelves.*.shelf_depth"  => "required|numeric|min:15|max:300",
            'comment' => 'nullable',
        ];
    }
}

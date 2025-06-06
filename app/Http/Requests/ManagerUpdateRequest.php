<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagerUpdateRequest extends FormRequest
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
            'name' => 'required',
            'login' => [
                'required',
                Rule::unique('users', 'login')->ignore($this->id)
            ],
            'password' => 'nullable|min:8',
            'shops' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                 => 'Имя является обязательным поле',
            'email.required'                => 'E-mail является обязательным поле',
            'email.unique'                  => 'Администратор с таким E-mail-ом уже добавлен',
            // 'password.required'             => 'Пароль является обязательным поле',
            'password.min'                  => 'Пароль должен содержать минимум 8 символов',
            'roles.required'                => 'Роль является обязательным поле',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $password_confirmation
 * @property string $role
 * @property string|null $phone
 * @property string|null $bio
 * @property \Illuminate\Http\UploadedFile|null $avatar
 * @property string|null $referral_code
 * @property bool $terms
 * @property bool $email_notifications
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:murid,pengajar'],
            'phone' => ['nullable', 'string', 'regex:/^(\+62|62|0)[0-9]{9,12}$/'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'referral_code' => ['nullable', 'exists:users,referral_code'],
            'terms' => ['required', 'accepted'],
            'email_notifications' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah terdaftar.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $phone
 * @property \Illuminate\Http\UploadedFile|null $avatar
 * @property string|null $referral_code
 * @property bool $terms
 * @property bool $email_notifications
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can register
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:murid,pengajar'], // Removed admin for security
            'phone' => ['nullable', 'string', 'regex:/^(\+62|62|0)[0-9]{9,12}$/', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'referral_code' => ['nullable', 'string', 'max:50', 'exists:users,referral_code'],
            'terms' => ['accepted'],
            'email_notifications' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus salah satu dari: murid atau pengajar.',
            'phone.regex' => 'Nomor telepon tidak valid. Gunakan format: +62 atau 0 diikuti 9-12 digit.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'File harus berupa JPG, PNG, atau GIF.',
            'avatar.max' => 'Ukuran gambar maksimal 2 MB.',
            'referral_code.exists' => 'Kode referral tidak ditemukan.',
            'referral_code.max' => 'Kode referral terlalu panjang.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan.',
        ];
    }
}

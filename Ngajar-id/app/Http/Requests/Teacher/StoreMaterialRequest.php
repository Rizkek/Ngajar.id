<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $class_id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property int $order
 * @property \Illuminate\Http\UploadedFile|null $video
 * @property string|null $video_url
 */
class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'pengajar';
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'integer', 'exists:kelas,kelas_id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', 'in:video,document,quiz'],
            'order' => ['required', 'integer', 'min:1'],
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi', 'max:524288'], // 500MB
            'video_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'video.max' => 'Video file must not exceed 500MB',
        ];
    }
}

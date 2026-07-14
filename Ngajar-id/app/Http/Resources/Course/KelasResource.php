<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KelasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->kelas_id,
            'title' => $this->judul,
            'description' => $this->deskripsi,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'teacher_id' => $this->pengajar_id,
            'teacher' => new UserResource($this->whenLoaded('pengajar')),
            'category' => $this->kategori,
            'level' => $this->level,
            'status' => $this->status,
            'price' => $this->harga,
            'price_token' => $this->harga_token,
            'rating' => $this->rating,
            'total_students' => $this->total_siswa,
            'duration' => $this->durasi,
            'materials_count' => $this->whenCounted('materi'),
            'students_count' => $this->whenCounted('peserta'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

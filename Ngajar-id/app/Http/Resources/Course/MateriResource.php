<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MateriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->materi_id,
            'class_id' => $this->kelas_id,
            'title' => $this->judul,
            'description' => $this->deskripsi,
            'type' => $this->tipe, // video, pdf, soal
            'file_url' => $this->file_url ? asset('storage/' . $this->file_url) : null,
            'is_premium' => (bool)$this->is_premium,
            'price_token' => $this->is_premium ? $this->harga_token : null,
            'duration' => $this->durasi ?? null,
            'order' => $this->urutan,
            'class' => new KelasResource($this->whenLoaded('kelas')),
            'completion_rate' => $this->completion_rate ?? 0,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

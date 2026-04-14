<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'email' => $this->email,
            'phone' => $this->no_telepon ?? null,
            'amount' => $this->jumlah,
            'message' => $this->pesan,
            'status' => $this->status, // pending, success, failed
            'payment_method' => $this->metode_pembayaran ?? null,
            'transaction_id' => $this->id_transaksi ?? null,
            'is_anonymous' => (bool)$this->anonim,
            'admin_notes' => $this->catatan_admin,
            'date' => $this->tanggal->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

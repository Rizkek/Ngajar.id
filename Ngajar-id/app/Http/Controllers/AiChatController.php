<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    /**
     * System prompt kepribadian & pengetahuan "Ngaji" - Asisten Ngajar.id
     */
    private string $systemPrompt = <<<PROMPT
Kamu adalah Asisten Virtual Ngajar.id bernama "Ngaji" — asisten AI yang ramah, cerdas, dan helpful untuk platform pendidikan Ngajar.id.

Tentang Ngajar.id:
- Platform pendidikan inklusif GRATIS yang menghubungkan pelajar dengan relawan pengajar di seluruh Indonesia
- Fitur utama: Kelas Online, Materi Gratis, Kelas Live, Diskusi, Catatan, Learning Path, Donasi, Sistem Token & XP
- Role pengguna: Murid (pelajar), Pengajar (relawan), Admin
- Cara daftar Murid: buka /register, pilih role "Murid", isi nama, email, password, klik Daftar
- Cara daftar Pengajar: buka /register, pilih role "Relawan Pengajar", isi form, tunggu verifikasi admin
- Lupa password: buka /password/reset dan masukkan email terdaftar, cek inbox email
- Token: mata uang platform untuk membuka konten premium. Diperoleh dari aktivitas belajar atau topup
- XP & Level: poin pengalaman dari aktivitas belajar, menentukan level pengguna
- Donasi: membantu operasional platform dan beasiswa pelajar tidak mampu
- Kelas gratis: semua kelas dasar bisa diakses tanpa token

Cara kamu menjawab:
- Gunakan Bahasa Indonesia yang santai tapi tetap informatif
- Jawab singkat dan jelas, maksimal 3-4 kalimat
- Gunakan emoji secukupnya untuk membuat percakapan lebih hidup 😊
- Jika pertanyaan tidak bisa dijawab, arahkan ke WhatsApp Admin: wa.me/6281234567890 atau email halo@ngajar.id
- JANGAN membahas hal di luar konteks pendidikan dan Ngajar.id
- Jika ditanya hal berbahaya atau tidak relevan, tolak sopan dan redirect ke topik Ngajar.id
PROMPT;

    /**
     * Handle pesan chat dari user → Groq API → kembalikan respons AI
     * Groq menggunakan format OpenAI-compatible (lebih cepat & tidak diblokir ISP)
     */
    public function chat(Request $request)
    {
        set_time_limit(25);

        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
            'history.*.role' => 'in:user,model',
            'history.*.text' => 'string|max:500',
        ]);

        $apiKey = config('services.groq.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan AI belum dikonfigurasi. Hubungi admin. 🔧',
            ], 503);
        }

        // Groq pakai format OpenAI: array of {role, content}
        // role: 'system' | 'user' | 'assistant'
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt],
        ];

        // Tambahkan history percakapan (konversi 'model' → 'assistant' untuk Groq)
        foreach ($request->history ?? [] as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'model' ? 'assistant' : 'user',
                'content' => $msg['text'],
            ];
        }

        // Tambahkan pesan user terbaru
        $messages[] = [
            'role' => 'user',
            'content' => $request->message,
        ];

        try {
            $response = Http::timeout(15)
                ->connectTimeout(10)
                ->withoutVerifying()           // Fix: SSL cert issue di Windows dev (cURL error 60)
                ->withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.1-8b-instant', // Cepat & gratis
                    'messages' => $messages,
                    'max_tokens' => 200,
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, asisten sedang tidak tersedia. Coba lagi sebentar. 🙏',
                ], 502);
            }

            $data = $response->json();
            $reply = $data['choices'][0]['message']['content']
                ?? 'Maaf, saya tidak bisa memproses permintaan ini.';

            return response()->json([
                'success' => true,
                'reply' => trim($reply),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq Connection Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke server AI gagal. Periksa koneksi internet kamu. 🌐',
            ], 504);
        }
    }
}

@extends('layouts.app')

@section('title', 'Pesan Baru - Ngajar.ID')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Buat Pesan Baru</h2>

            <form action="{{ route('messages.store') }}" method="POST">
                @csrf

                <!-- Select Recipient -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Tujuan Pesan</label>
                    @if($receiver)
                        <div class="flex items-center gap-3 p-4 bg-teal-50 border border-teal-200 rounded-lg">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($receiver->name) }}&background=random"
                                    alt="{{ $receiver->name }}" class="w-full h-full object-cover rounded-full">
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900">{{ $receiver->name }}</p>
                                <p class="text-xs text-slate-600">@if($receiver->role == 'pelajar') Pelajar @elseif($receiver->role == 'pengajar') Pengajar @else Admin @endif</p>
                            </div>
                        </div>
                        <input type="hidden" name="receiver_id" value="{{ $receiver->user_id }}">
                    @else
                        <select name="receiver_id" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-teal-500 focus:outline-none">
                            <option value="">-- Pilih Penerima --</option>
                            @foreach(\App\Models\User::where('user_id', '!=', Auth::id())->get() as $user)
                                <option value="{{ $user->user_id }}">
                                    {{ $user->name }} ({{ $user->role == 'pelajar' ? 'Pelajar' : ($user->role == 'pengajar' ? 'Pengajar' : 'Admin') }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Subject -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Subjek</label>
                    <input type="text" name="subject" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-teal-500 focus:outline-none"
                        placeholder="Subjek pesan...">
                    @error('subject')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Pesan</label>
                    <textarea name="message" required rows="8"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-teal-500 focus:outline-none"
                        placeholder="Tulis pesan Anda di sini..."></textarea>
                    @error('message')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-lg transition">
                        <span class="material-symbols-rounded inline mr-2">send</span>
                        Kirim Pesan
                    </button>
                    <a href="{{ route('messages.inbox') }}"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-slate-900 font-bold py-3 rounded-lg transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

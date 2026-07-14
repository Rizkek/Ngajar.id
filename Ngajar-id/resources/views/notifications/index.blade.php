@extends('layouts.dashboard')

@section('title', 'Notifikasi - Ngajar.ID')
@section('header_title', 'Notifikasi')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Inbox Notifikasi</h2>
        <p class="text-gray-500 text-sm mt-1">Pusat informasi dan pembaruan akun Anda</p>
    </div>
    
    @if($notifications->count() > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="text-teal-600 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-lg text-sm transition font-bold flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">done_all</span> Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @if($notifications->isEmpty())
        <div class="text-center py-24">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100">
                <span class="material-symbols-rounded text-5xl text-gray-300">notifications_off</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Tidak ada notifikasi</h3>
            <p class="text-gray-500 mt-2">Anda telah membaca semua pembaruan terbaru.</p>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($notifications as $notification)
                <div class="p-6 hover:bg-gray-50 transition-colors {{ is_null($notification->read_at) ? 'bg-teal-50/30' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center {{ is_null($notification->read_at) ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 text-gray-500' }}">
                            <span class="material-symbols-rounded">
                                {{ $notification->data['icon'] ?? 'notifications' }}
                            </span>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-gray-900 truncate">
                                    {{ $notification->data['title'] ?? 'Pemberitahuan Baru' }}
                                </h4>
                                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                {{ $notification->data['message'] ?? $notification->data['judul_materi'] ?? '' }}
                            </p>
                            
                            @if(is_null($notification->read_at))
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-teal-600 hover:text-teal-800 flex items-center gap-1">
                                        <span class="material-symbols-rounded text-[14px]">check</span> Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($notifications->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

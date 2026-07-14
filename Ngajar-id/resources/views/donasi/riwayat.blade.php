@extends('layouts.app')

@section('title', 'Riwayat Donasi Publik - Ngajar.ID')

@section('content')
<div class="bg-teal-700 pb-24 pt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-white font-robotoSlab sm:text-4xl">Riwayat Transparansi Donasi</h1>
            <p class="mt-4 max-w-2xl text-xl text-teal-100 mx-auto">Terima kasih kepada seluruh orang baik yang telah berkontribusi memajukan pendidikan di Indonesia.</p>
        </div>
    </div>
</div>

<div class="-mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        @if($riwayat_donasi->isEmpty())
            <div class="text-center py-20">
                <span class="material-symbols-rounded text-6xl text-gray-300 mb-4 block">receipt_long</span>
                <h3 class="text-lg font-medium text-gray-900">Belum Ada Donasi Tercatat</h3>
                <p class="mt-2 text-sm text-gray-500">Jadilah yang pertama untuk berkontribusi.</p>
                <a href="{{ route('donasi') }}" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-full shadow-sm text-white bg-teal-600 hover:bg-teal-700 transition">
                    Mulai Donasi
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Donatur</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pesan</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($riwayat_donasi as $donasi)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($donasi->tanggal)->translatedFormat('d F Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-sm">
                                            {{ substr($donasi->nama_donatur ?? 'H', 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $donasi->anonim ? 'Hamba Allah' : ($donasi->nama_donatur ?? 'Hamba Allah') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    <span title="{{ $donasi->pesan }}">{{ $donasi->pesan ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-teal-600 text-right">
                                    Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(in_array($donasi->status, ['paid', 'settlement', 'capture']))
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            Berhasil
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            Tertunda
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $riwayat_donasi->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
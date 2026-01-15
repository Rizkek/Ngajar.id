@extends('layouts.app')

@section('title', 'Donasi - Ngajar.ID')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Total Donasi -->
        <div class="bg-teal-600 text-white text-center py-10 rounded-lg mb-8 shadow-lg">
            <h2 class="text-3xl md:text-5xl font-bold uppercase mb-2">Total Donasi</h2>
            <p class="text-4xl md:text-7xl font-bold">Rp {{ number_format($total_donasi, 0, ',', '.') }}</p>
        </div>

        <!-- Riwayat Donasi -->
        <h3 class="text-teal-500 text-2xl font-bold text-center mb-6">Riwayat Donasi</h3>

        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="w-full table-auto border-collapse border border-teal-500 text-center">
                <thead class="bg-teal-50 text-teal-700 font-bold uppercase text-sm">
                    <tr>
                        <th class="border border-teal-500 px-4 py-3">Nama</th>
                        <th class="border border-teal-500 px-4 py-3">Jumlah Donasi</th>
                        <th class="border border-teal-500 px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($riwayat_donasi as $donasi)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-teal-300 px-4 py-2">{{ htmlspecialchars($donasi['nama']) }}</td>
                            <td class="border border-teal-300 px-4 py-2">Rp {{ number_format($donasi['jumlah'], 0, ',', '.') }}
                            </td>
                            <td class="border border-teal-300 px-4 py-2">{{ date('d-m-Y H:i', strtotime($donasi['tanggal'])) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="border border-teal-300 px-4 py-4 text-gray-500">Belum ada donasi tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Penjelasan Donasi -->
        <div class="mt-10 bg-gray-100 rounded-lg p-6 text-justify shadow-sm">
            <h3 class="text-xl font-bold text-teal-700 mb-4">Tentang Donasi di Ngajar.ID</h3>
            <p class="mb-4 text-gray-800">
                Donasi yang terkumpul di <strong>Ngajar.ID</strong> bertujuan untuk membantu pelajar dari keluarga kurang
                mampu agar tetap dapat mengakses pendidikan secara gratis dan layak.
                Kami percaya bahwa setiap anak berhak mendapatkan kesempatan belajar tanpa terkendala biaya.
            </p>
            <p class="mb-4 text-gray-800">
                Dana yang Anda donasikan akan disalurkan dalam bentuk:
            </p>
            <ul class="list-disc ml-6 text-gray-800 mb-4">
                <li>Beasiswa pendidikan bagi siswa berprestasi namun terkendala biaya</li>
                <li>Paket belajar seperti modul, kuota internet, dan perangkat pendukung</li>
                <li>Pelatihan dan pendampingan belajar secara daring bersama relawan pengajar</li>
            </ul>
            <p class="text-gray-800">
                Transparansi adalah komitmen kami. Anda dapat melihat jumlah total donasi dan riwayat kontribusi pada
                halaman ini secara langsung dan real-time.
                Setiap rupiah yang Anda berikan sangat berarti dalam membuka masa depan cerah bagi para pelajar di seluruh
                Indonesia.
            </p>
        </div>
    </div>
@endsection
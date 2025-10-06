{{-- Tambahkan blok PHP untuk memeriksa waktu --}}
@php
$currentTime = \Carbon\Carbon::now('Asia/Makassar');
$endOfDay = $currentTime->copy()->setTime(14, 0, 0);
$isPastOfficeHours = $currentTime->gt($endOfDay);
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in as Admin!") }}

                    {{-- Kotak Aksi Admin --}}
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                        {{-- Tampilkan tombol hanya jika sudah lewat jam 14:00 --}}
                        @if ($isPastOfficeHours)
                        <form action="{{ route('admin.attendance.mark_alpa') }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menandai semua karyawan yang belum absen sebagai ALPA untuk hari ini? Tindakan ini tidak bisa dibatalkan.');">
                            @csrf
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                                <i class="fas fa-user-times mr-2"></i>
                                Tandai Karyawan yg Alpa Hari Ini
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 mt-2">
                            * Klik tombol ini untuk secara otomatis mengisi status "Alpa" bagi karyawan yang tidak
                            melakukan absensi sama sekali hari ini.
                        </p>
                        @else
                        {{-- Tampilkan pesan jika belum waktunya --}}
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                            <p class="font-bold">Informasi</p>
                            <p>Tombol untuk memproses absensi "Alpa" akan muncul di sini setelah jam kerja berakhir
                                (pukul 14:00 WITA).</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
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
            @if (session('success'))
            <div class="mt-4 mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif
            @if (session('info'))
            <div class="mt-4 mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <p>{{ session('info') }}</p>
            </div>
            @endif
            {{-- Bagian Statistik Kartu --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center space-x-4">
                    <div class="bg-blue-500 p-3 rounded-full">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Karyawan Aktif</p>
                        <p class="text-2xl font-bold">{{ $totalEmployees }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center space-x-4">
                    <div class="bg-green-500 p-3 rounded-full">
                        <i class="fas fa-user-check text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Hadir Hari Ini</p>
                        <p class="text-2xl font-bold">{{ $presentToday }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center space-x-4">
                    <div class="bg-yellow-500 p-3 rounded-full">
                        <i class="fas fa-user-clock text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Terlambat Hari Ini</p>
                        <p class="text-2xl font-bold">{{ $lateToday }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center space-x-4">
                    <div class="bg-orange-500 p-3 rounded-full">
                        <i class="fas fa-user-times text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Izin / Sakit Hari Ini</p>
                        <p class="text-2xl font-bold">{{ $onLeaveToday }}</p>
                    </div>
                </div>
            </div>

            {{-- Bagian Aktivitas Terbaru & Aksi Cepat --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Aktivitas Absensi Terbaru Hari Ini</h3>
                        <div class="space-y-4">
                            @forelse ($recentActivities as $activity)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold">{{ $activity->employee->nama_lengkap }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($activity->time_in && $activity->time_out)
                                        Absen Pulang pada {{ $activity->time_out }}
                                        @elseif($activity->time_in)
                                        Absen Masuk pada {{ $activity->time_in }}
                                        @else
                                        Mengajukan {{ $activity->status }}
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    {{-- Badge Status --}}
                                    @php
                                    $status = $activity->status;
                                    $badgeColor = 'bg-red-100 text-red-800'; // Default
                                    if (str_contains($status, 'Hadir')) $badgeColor = 'bg-green-100 text-green-800';
                                    elseif (str_contains($status, 'Terlambat')) $badgeColor = 'bg-yellow-100
                                    text-yellow-800';
                                    elseif (str_contains($status, 'Izin')) $badgeColor = 'bg-blue-100 text-blue-800';
                                    elseif (str_contains($status, 'Sakit')) $badgeColor = 'bg-orange-100
                                    text-orange-800';
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                        {{ $activity->status }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500">Belum ada aktivitas absensi hari ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                        @if ($isPastOfficeHours)
                        <form action="{{ route('admin.attendance.mark_alpa') }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menandai semua karyawan yang belum absen sebagai ALPA untuk hari ini? Tindakan ini tidak bisa dibatalkan.');">
                            @csrf
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                <i class="fas fa-user-slash mr-2"></i>
                                Tandai Karyawan yg Alpa
                            </button>
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                Klik untuk menandai karyawan yang tidak absen hari ini sebagai "Alpa".
                            </p>
                        </form>
                        @else
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                            <p>Tombol "Proses Karyawan Alpa" akan aktif setelah jam 14:00 WITA.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
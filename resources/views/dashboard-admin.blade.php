@php
// Logic untuk enable/disable tombol Mark Alpa
$currentTime = \Carbon\Carbon::now('Asia/Makassar');
$endOfDay = $currentTime->copy()->setTime(14, 0, 0);
$isPastOfficeHours = $currentTime->gt($endOfDay);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ \Carbon\Carbon::now('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }}</span>
                <span class="ml-3">{{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i') }} WITA</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 🎉 Success Messages --}}
            @if (session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm animate-pulse" role="alert">
                <div class="flex">
                    <span class="text-xl mr-2"></span>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            @endif
            
            {{-- ℹ️ Info Messages --}}
            @if (session('info'))
            <div class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-700 px-4 py-3 rounded-lg shadow-sm" role="alert">
                <div class="flex">
                    <span class="text-xl mr-2"></span>
                    <p>{{ session('info') }}</p>
                </div>
            </div>
            @endif

            {{-- 📊 Bagian Statistik Kartu --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                
                {{-- Card 1: Total Karyawan --}}
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <p class="text-sm opacity-90 mb-1">Total Karyawan Aktif</p>
                            <p class="text-4xl font-bold">{{ $totalEmployees }}</p>
                            <p class="text-xs opacity-75 mt-2">Registered</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-4 rounded-full">
                            <i class="fas fa-users text-white text-3xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Hadir Hari Ini --}}
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <p class="text-sm opacity-90 mb-1">Hadir Hari Ini</p>
                            <p class="text-4xl font-bold">{{ $presentToday }}</p>
                            <p class="text-xs opacity-75 mt-2">Present</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-4 rounded-full">
                            <i class="fas fa-user-check text-white text-3xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Terlambat --}}
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <p class="text-sm opacity-90 mb-1">Terlambat Hari Ini</p>
                            <p class="text-4xl font-bold">{{ $lateToday }}</p>
                            <p class="text-xs opacity-75 mt-2">Late Arrival</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-4 rounded-full">
                            <i class="fas fa-user-clock text-white text-3xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Izin/Sakit --}}
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <p class="text-sm opacity-90 mb-1">Izin / Sakit</p>
                            <p class="text-4xl font-bold">{{ $onLeaveToday }}</p>
                            <p class="text-xs opacity-75 mt-2">On Leave</p>
                        </div>
                        <div class="bg-white bg-opacity-20 p-4 rounded-full">
                            <i class="fas fa-user-times text-white text-3xl"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 📋 Bagian Aktivitas Terbaru & Aksi Cepat --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- 📋 Aktivitas Terbaru (2/3 width) --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-lg sm:rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <span class="text-2xl mr-2"></span>
                                Aktivitas Absensi Terbaru
                            </h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                Hari Ini
                            </span>
                        </div>

                        <div class="space-y-3">
                            @forelse ($recentActivities as $activity)
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg hover:shadow-md transition-shadow border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-indigo-100 p-3 rounded-full">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{ $activity->employee->nama_lengkap }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            @if($activity->time_in && $activity->time_out)
                                                Absen Pulang pada <span class="font-medium">{{ $activity->time_out }}</span>
                                            @elseif($activity->time_in)
                                                Absen Masuk pada <span class="font-medium">{{ $activity->time_in }}</span>
                                            @else
                                                Mengajukan {{ $activity->status }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    {{-- 🎨 Badge Status dengan Warna --}}
                                    @php
                                        $status = $activity->status;
                                        $badgeColor = 'bg-gray-100 text-gray-800';
                                        
                                        if (str_contains($status, 'Hadir')) 
                                            $badgeColor = 'bg-green-100 text-green-800 border-green-300';
                                        elseif (str_contains($status, 'Terlambat')) 
                                            $badgeColor = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                        elseif (str_contains($status, 'Izin')) 
                                            $badgeColor = 'bg-blue-100 text-blue-800 border-blue-300';
                                        elseif (str_contains($status, 'Sakit')) 
                                            $badgeColor = 'bg-orange-100 text-orange-800 border-orange-300';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                        {{ $activity->status }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center py-12">
                                <span class="text-6xl mb-3"></span>
                                <p class="text-gray-500 text-lg font-medium">Belum ada aktivitas hari ini</p>
                                <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah karyawan mulai absen</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ⚡ Aksi Cepat (1/3 width) --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <span class="text-2xl mr-2"></span>
                            Aksi Cepat
                        </h3>
                        
                        @if ($isPastOfficeHours)
                            {{-- Tombol Aktif setelah jam 14:00 --}}
                            <form action="{{ route('admin.attendance.mark_alpa') }}" 
                                  method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menandai semua karyawan yang belum absen sebagai ALPA untuk hari ini?\n\n❗ Tindakan ini tidak bisa dibatalkan!');">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                                    <i class="fas fa-user-slash mr-2"></i>
                                    <span class="text-lg">Tandai Alpa</span>
                                </button>
                                <p class="text-xs text-gray-500 mt-3 text-center leading-relaxed">
                                    Klik untuk menandai karyawan yang tidak absen hari ini sebagai "Alpa"
                                </p>
                            </form>
                        @else
                            {{-- Info Box kalau belum waktunya --}}
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <span class="text-2xl"></span>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-bold text-blue-800 mb-1">
                                            Tombol Belum Aktif
                                        </h4>
                                        <p class="text-xs text-blue-700 leading-relaxed">
                                            Tombol "Tandai Alpa" akan aktif setelah <strong>jam 14:00 WITA</strong>.
                                        </p>
                                        <div class="mt-3 pt-3 border-t border-blue-200">
                                            <p class="text-xs text-blue-600">
                                                Waktu sekarang: <span class="font-semibold">{{ $currentTime->format('H:i') }} WITA</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Quick Stats Mini --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Stats</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Persentase Kehadiran:</span>
                                    <span class="font-semibold text-green-600">
                                        {{ $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Belum Absen:</span>
                                    <span class="font-semibold text-red-600">
                                        {{ $totalEmployees - $presentToday - $onLeaveToday }} orang
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
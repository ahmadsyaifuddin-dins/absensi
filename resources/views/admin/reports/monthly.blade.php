<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('admin.reports.monthly') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="employee_id" class="block text-sm font-medium text-gray-700">Pilih
                                    Karyawan:</label>
                                <select name="employee_id" id="employee_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                    required>
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $selectedEmployeeId==$employee->id ?
                                        'selected' : '' }}>
                                        {{ $employee->nama_lengkap }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Bulan:</label>
                                <select name="month" id="month"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 ... rounded-md">
                                    @for ($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ $selectedMonth==$m
                                        ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                        </option>
                                        @endfor
                                </select>
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Tahun:</label>
                                <input type="number" name="year" id="year" value="{{ $selectedYear }}"
                                    class="mt-1 block w-full ... rounded-md">
                            </div>
                            <div>
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Tampilkan hasil hanya jika karyawan dipilih --}}
                    @if ($selectedEmployeeId && !empty($recap))
                    <div class="mb-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">
                                Rekapitulasi untuk Bulan {{
                                \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') }} {{ $selectedYear
                                }}
                            </h3>
                            {{-- TOMBOL EXPORT BARU --}}
                            <a href="{{ route('admin.reports.monthly.export', ['employee_id' => $selectedEmployeeId, 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                <i class="fas fa-file-excel mr-2"></i> Export ke Excel
                            </a>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-2 text-center">
                            <div class="p-4 bg-green-100 rounded-lg">
                                <p class="text-sm">Hadir</p>
                                <p class="font-bold text-2xl">{{ $recap['hadir'] }}</p>
                            </div>
                            <div class="p-4 bg-yellow-100 rounded-lg">
                                <p class="text-sm">Terlambat</p>
                                <p class="font-bold text-2xl">{{ $recap['terlambat'] }}</p>
                            </div>
                            <div class="p-4 bg-blue-100 rounded-lg">
                                <p class="text-sm">Izin</p>
                                <p class="font-bold text-2xl">{{ $recap['izin'] }}</p>
                            </div>
                            <div class="p-4 bg-orange-100 rounded-lg">
                                <p class="text-sm">Sakit</p>
                                <p class="font-bold text-2xl">{{ $recap['sakit'] }}</p>
                            </div>
                            <div class="p-4 bg-red-100 rounded-lg">
                                <p class="text-sm">Alpa</p>
                                <p class="font-bold text-2xl">{{ $recap['alpa'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam
                                        Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam
                                        Pulang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                <tr class="bg-white">
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($attendance->date)->isoFormat('dddd,
                                        D MMMM Y') }}</td>
                                    <td class="px-6 py-4">{{ $attendance->time_in ?? '--:--' }}</td>
                                    <td class="px-6 py-4">{{ $attendance->time_out ?? '--:--' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if(str_contains($attendance->status, 'Hadir')) bg-green-100 text-green-800 
                                                    @elseif(str_contains($attendance->status, 'Terlambat')) bg-yellow-100 text-yellow-800 
                                                    @elseif($attendance->status == 'Izin') bg-blue-100 text-blue-800
                                                    @elseif($attendance->status == 'Sakit') bg-orange-100 text-orange-800
                                                    @elseif($attendance->status == 'Alpa') bg-red-100 text-red-800
                                                    @endif">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">Tidak ada data absensi pada periode
                                        ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-gray-500">Silakan pilih karyawan dan periode untuk menampilkan laporan.
                    </p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
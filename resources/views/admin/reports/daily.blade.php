
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Filter Tanggal --}}
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.reports.daily') }}">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                                    <input type="date" name="date" id="date" value="{{ $selectedDate }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                </div>
                                <div class="pt-5">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        Tampilkan Laporan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tabel Laporan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Karyawan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $attendance->employee->nama_lengkap ?? 'Karyawan Tidak Ditemukan' }}</td>
                                        <td class="px-6 py-4">{{ $attendance->time_in ?? '--:--' }}</td>
                                        <td class="px-6 py-4">{{ $attendance->time_out ?? '--:--' }}</td>
                                        <td class="px-6 py-4">
                                            {{-- Logika Badge Status --}}
                                            @php
                                                $status = $attendance->status;
                                                $badgeColor = 'bg-gray-100 text-gray-800'; // Default
                                                if (str_contains($status, 'Hadir')) $badgeColor = 'bg-green-100 text-green-800';
                                                elseif (str_contains($status, 'Terlambat')) $badgeColor = 'bg-yellow-100 text-yellow-800';
                                                elseif (str_contains($status, 'Izin')) $badgeColor = 'bg-blue-100 text-blue-800';
                                                elseif (str_contains($status, 'Sakit')) $badgeColor = 'bg-orange-100 text-orange-800';
                                                elseif (str_contains($status, 'Alpa')) $badgeColor = 'bg-red-100 text-red-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->notes }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada data absensi untuk tanggal yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
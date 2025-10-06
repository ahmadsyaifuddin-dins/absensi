<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan pesan sukses atau error --}}
                    @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">Gagal!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    @endif

                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ \Carbon\Carbon::now('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }}
                        </h3>
                        <div id="jam-digital" class="text-5xl font-bold text-gray-800 my-4">00:00:00</div>

                        <div class="mt-6">
                            {{-- LOGIKA BARU DIMULAI DI SINI --}}
                            @php
                            $currentTime = \Carbon\Carbon::now('Asia/Makassar');
                            $endOfDay = $currentTime->copy()->setTime(14, 0, 0);
                            $isPastOfficeHours = $currentTime->gt($endOfDay);
                            @endphp

                            @if (isset($todaysAttendance))
                            {{-- JIKA SUDAH ADA DATA ABSENSI HARI INI --}}
                            @if (in_array($todaysAttendance->status, ['Izin', 'Sakit']))
                            <div class="bg-yellow-100 text-yellow-800 font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                Anda hari ini tercatat: <strong>{{ $todaysAttendance->status }}</strong>.
                            </div>
                            @elseif ($todaysAttendance->status === 'Alpa')
                            {{-- Jika statusnya Alpa --}}
                            <div class="bg-red-100 text-red-800 font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                Anda hari ini tercatat: <strong>Alpa</strong>.
                            </div>
                            @elseif ($todaysAttendance->time_out === null)
                            <form method="POST" action="{{ route('attendance.clockout') }}" class="inline-block">
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang
                                </button>
                            </form>
                            @else
                            <div class="bg-blue-100 text-blue-800 font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                Absensi hari ini telah selesai.
                            </div>
                            @endif
                            @else
                            {{-- JIKA BELUM ADA DATA ABSENSI HARI INI --}}
                            @if ($isPastOfficeHours)
                            {{-- Jika sudah lewat jam 14:00 --}}
                            <div class="bg-red-100 text-red-800 font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                Waktu untuk melakukan absensi hari ini telah berakhir.
                            </div>
                            @else
                            {{-- Jika masih dalam jam kerja --}}
                            <div class="space-x-4">
                                <form method="POST" action="{{ route('attendance.clockin') }}" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                        <i class="fas fa-fingerprint mr-2"></i> Absen Masuk
                                    </button>
                                </form>
                                <button onclick="openPermissionModal('Izin')"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                    <i class="fas fa-file-alt mr-2"></i> Ajukan Izin
                                </button>
                                <button onclick="openPermissionModal('Sakit')"
                                    class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                    <i class="fas fa-notes-medical mr-2"></i> Ajukan Sakit
                                </button>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    {{-- Ringkasan Absensi --}}
                    <div class="mt-10 border-t pt-6">
                        <h4 class="text-md font-semibold mb-4">Ringkasan Hari Ini:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <p class="text-sm text-blue-700">Jam Masuk</p>
                                <p class="text-lg font-bold text-blue-900">{{ $todaysAttendance?->time_in ?? '--:--' }}
                                </p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-lg">
                                <p class="text-sm text-purple-700">Jam Pulang</p>
                                <p class="text-lg font-bold text-purple-900">{{ $todaysAttendance?->time_out ?? '--:--'
                                    }}</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg">
                                <p class="text-sm text-yellow-700">Status</p>
                                <p class="text-lg font-bold text-yellow-900">{{ $todaysAttendance?->status ?? 'Belum
                                    Absen' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UNTUK IZIN/SAKIT --}}
    <div id="permissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Form Pengajuan</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="permissionForm" method="POST" action="{{ route('attendance.store_permission') }}">
                        @csrf
                        <input type="hidden" name="status" id="statusInput">
                        <textarea name="notes"
                            class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" rows="4"
                            placeholder="Tuliskan keterangan Anda di sini..." required></textarea>
                        <div class="items-center px-4 py-3">
                            <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
                <div class="items-center px-4 py-1">
                    <button onclick="closePermissionModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateClock() {
            const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Makassar' }));
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('jam-digital').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- JavaScript untuk Modal ---
        const modal = document.getElementById('permissionModal');
        const modalTitle = document.getElementById('modalTitle');
        const statusInput = document.getElementById('statusInput');

        function openPermissionModal(status) {
            modal.classList.remove('hidden');
            modalTitle.textContent = `Form Pengajuan ${status}`;
            statusInput.value = status;
        }

        function closePermissionModal() {
            modal.classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
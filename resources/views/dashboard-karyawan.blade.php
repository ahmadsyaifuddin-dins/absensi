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

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Gagal!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ \Carbon\Carbon::now('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }}
                        </h3>
                        <div id="jam-digital" class="text-5xl font-bold text-gray-800 my-4">00:00:00</div>

                        <div class="mt-6 space-x-4">
                            @if (isset($todaysAttendance))
                                @if ($todaysAttendance->time_out === null)
                                    <form method="POST" action="{{ route('attendance.clockout') }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-lg text-xl shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-blue-100 text-blue-800 font-bold py-4 px-8 rounded-lg text-xl shadow-lg">
                                        Absensi hari ini telah selesai.
                                    </div>
                                @endif
                            @else
                                <form method="POST" action="{{ route('attendance.clockin') }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg text-xl shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                                        <i class="fas fa-fingerprint mr-2"></i> Absen Masuk
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="mt-10 border-t pt-6">
                        <h4 class="text-md font-semibold mb-4">Ringkasan Hari Ini:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <p class="text-sm text-blue-700">Jam Masuk</p>
                                <p class="text-lg font-bold text-blue-900">{{ $todaysAttendance?->time_in ?? '--:--' }}</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-lg">
                                <p class="text-sm text-purple-700">Jam Pulang</p>
                                <p class="text-lg font-bold text-purple-900">{{ $todaysAttendance?->time_out ?? '--:--' }}</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg">
                                <p class="text-sm text-yellow-700">Status</p>
                                <p class="text-lg font-bold text-yellow-900">{{ $todaysAttendance?->status ?? 'Belum Absen' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateClock() {
            // WITA is UTC+8
            const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Makassar' }));
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('jam-digital').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @endpush
</x-app-layout>
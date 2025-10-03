<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <p class="text-gray-600">Nama: <span class="font-medium text-gray-900">{{ $user->name }}</span></p>
                        <p class="text-gray-600">Email: <span class="font-medium text-gray-900">{{ $user->email }}</span></p>
                        <p class="text-gray-600">Jenis Kelamin: <span class="font-medium text-gray-900">{{ $user->gender }}</span></p>
                        <p class="text-gray-600">Nomor HP: <span class="font-medium text-gray-900">{{ $user->phone }}</span></p>
                        <p class="text-gray-600">Peran: <span class="font-medium text-gray-900">{{ ucfirst($user->role) }}</span></p>
                        <p class="text-gray-600">Terdaftar Sejak: <span class="font-medium text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</span></p>
                    </div>
                    
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
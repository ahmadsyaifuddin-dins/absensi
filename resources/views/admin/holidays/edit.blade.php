<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hari Libur') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('holidays.update', $holiday) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="date" class="block font-medium text-sm text-gray-700">Tanggal</label>
                            <input id="date" type="date" name="date" value="{{ $holiday->date }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Keterangan</label>
                            <input id="description" type="text" name="description" value="{{ $holiday->description }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="ml-4 bg-indigo-500 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
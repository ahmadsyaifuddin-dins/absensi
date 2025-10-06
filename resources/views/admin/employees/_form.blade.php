{{-- Error Messages --}}
@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Oops!</strong>
        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Form Fields --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="nama_lengkap" :value="__('Nama Lengkap (Sesuai KTP)')" />
        <x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text" name="nama_lengkap" :value="old('nama_lengkap', $employee->nama_lengkap ?? $user->name)" required autofocus />
    </div>
    <div>
        <x-input-label for="nip" :value="__('NIP')" />
        <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" maxlength="16" :value="old('nip', $employee->nip ?? '')" required />
    </div>
    <div>
        <x-input-label for="posisi" :value="__('Posisi')" />
        <x-text-input id="posisi" class="block mt-1 w-full" type="text" name="posisi" :value="old('posisi', $employee->posisi ?? '')" required />
    </div>
    <div>
        <x-input-label for="jabatan" :value="__('Jabatan')" />
        <x-text-input id="jabatan" class="block mt-1 w-full" type="text" name="jabatan" :value="old('jabatan', $employee->jabatan ?? '')" required />
    </div>
    <div>
        <x-input-label for="tanggal_perekrutan" :value="__('Tanggal Perekrutan')" />
        <x-text-input id="tanggal_perekrutan" class="block mt-1 w-full" type="date" name="tanggal_perekrutan" :value="old('tanggal_perekrutan', $employee->tanggal_perekrutan ?? '')" required />
    </div>
    <div>
        <x-input-label for="no_hp" :value="__('Nomor HP')" />
        <x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp" maxLength="15" :value="old('no_hp', $employee->no_hp ?? '')" required />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="alamat" :value="__('Alamat')" />
        <textarea name="alamat" id="alamat" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $employee->alamat ?? '') }}</textarea>
    </div>
    <div>
        <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
        <select name="jenis_kelamin" id="jenis_kelamin" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="Laki-laki" @selected(old('jenis_kelamin', $employee->jenis_kelamin ?? '') == 'Laki-laki')>Laki-laki</option>
            <option value="Perempuan" @selected(old('jenis_kelamin', $employee->jenis_kelamin ?? '') == 'Perempuan')>Perempuan</option>
        </select>
    </div>
     <div>
        <x-input-label for="status" :value="__('Status Karyawan')" />
        <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="aktif" @selected(old('status', $employee->status ?? 'aktif') == 'aktif')>Aktif</option>
            <option value="tidak aktif" @selected(old('status', $employee->status ?? '') == 'tidak aktif')>Tidak Aktif</option>
            <option value="dihentikan" @selected(old('status', $employee->status ?? '') == 'dihentikan')>Dihentikan</option>
        </select>
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('users.show', $user) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
        Batal
    </a>
    <x-primary-button>
        {{ isset($employee) ? 'Update Data' : 'Simpan Data' }}
    </x-primary-button>
</div>
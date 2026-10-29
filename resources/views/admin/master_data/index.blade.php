<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-emerald-800 leading-tight">
            {{ __('Master Data Unit Kerja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-emerald-500">
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Unit Kerja</h3>
                        <p class="text-sm text-gray-500">Kelola data unit kerja dan jumlah shift harian.</p>
                    </div>
                    <!-- Tombol ini sementara belum aktif, akan kita fungsikan di tahap selanjutnya -->
                    <button class="bg-emerald-600 text-white px-4 py-2 rounded shadow hover:bg-emerald-700 transition text-sm font-semibold">
                        + Tambah Unit
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-emerald-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-20 text-center">No.</th>
                                <th scope="col" class="px-6 py-3">Nama Unit Kerja</th>
                                <th scope="col" class="px-6 py-3 text-center">Jumlah Shift</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($units as $unit)
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-center font-medium text-gray-900">{{ $unit->no_urut }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $unit->nama }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-200">
                                        {{ $unit->jumlah_shift }} Shift
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <a href="#" class="text-amber-500 hover:text-amber-700 font-medium">Edit</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="#" class="text-red-500 hover:text-red-700 font-medium">Hapus</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
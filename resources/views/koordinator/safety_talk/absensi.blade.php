<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-800 leading-tight">
            {{ __('Rincian Karyawan Tidak Hadir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-red-500">
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                    <h3 class="text-lg font-bold text-red-800">Lengkapi Data Absensi</h3>
                    <p class="text-sm text-red-700">Berdasarkan laporan sebelumnya, terdapat <strong>{{ $safetyTalk->jml_tidak_hadir }} orang</strong> yang tidak hadir. Total jumlah orang yang Anda rincikan di bawah ini <strong>wajib sama</strong> dengan angka tersebut.</p>
                </div>

                <form action="{{ route('koordinator.safety-talk.store-absensi', $safetyTalk->id) }}" method="POST" class="space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 text-red-700 p-4 rounded-md mb-4 border border-red-200">
                            <ul class="list-disc pl-5 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="absensi-container" class="space-y-4">
                        <!-- Baris Pertama -->
                        <div class="baris-absen flex flex-col md:flex-row gap-4 items-start md:items-end bg-gray-50 p-4 rounded-md border border-gray-200 relative">
                            <div class="w-full md:w-1/3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select name="kategori[]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Terlambat">Terlambat</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Lain-lain">Lain-lain</option>
                                </select>
                            </div>
                            <div class="w-full md:w-1/4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Orang)</label>
                                <input type="number" name="jumlah[]" min="1" max="{{ $safetyTalk->jml_tidak_hadir }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required placeholder="Contoh: 2">
                            </div>
                            <div class="w-full md:w-5/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Nama Karyawan</label>
                                <input type="text" name="keterangan_nama[]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Contoh: Budi, Andi (Opsional)">
                            </div>
                            <button type="button" class="btn-hapus hidden absolute -top-3 -right-3 bg-red-100 text-red-600 rounded-full w-8 h-8 flex items-center justify-center border border-red-200 hover:bg-red-200 transition" title="Hapus baris">✖</button>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <button type="button" id="btn-tambah" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-900 transition font-semibold text-sm">
                            + Tambah Baris Rincian
                        </button>
                        <button type="submit" class="bg-amber-600 text-white px-6 py-2 rounded shadow hover:bg-amber-700 transition font-semibold">
                            Kirim Laporan Final ✔️
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script Tambah/Hapus Baris -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('absensi-container');
            const btnTambah = document.getElementById('btn-tambah');

            btnTambah.addEventListener('click', function() {
                const firstRow = container.querySelector('.baris-absen');
                const newRow = firstRow.cloneNode(true);
                newRow.querySelector('input[type="number"]').value = '';
                newRow.querySelector('input[type="text"]').value = '';
                newRow.querySelector('select').selectedIndex = 0;
                
                const btnHapus = newRow.querySelector('.btn-hapus');
                btnHapus.classList.remove('hidden');
                
                btnHapus.addEventListener('click', function() {
                    newRow.remove();
                });
                container.appendChild(newRow);
            });
        });
    </script>
</x-app-layout>
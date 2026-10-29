<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-800 leading-tight">
            {{ __('Riwayat Laporan Safety Talk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-amber-500">
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Riwayat Laporan Anda</h3>
                        <p class="text-sm text-gray-500">Daftar laporan Safety Talk yang telah Anda kirimkan.</p>
                    </div>
                    <a href="{{ route('koordinator.safety-talk.create') }}" class="bg-amber-600 text-white px-4 py-2 rounded shadow hover:bg-amber-700 transition text-sm font-semibold">
                        + Buat Laporan Baru
                    </a>
                </div>

                @if (session('success'))
                    <div class="bg-green-50 text-green-700 p-4 rounded-md mb-4 border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="bg-red-50 text-red-700 p-4 rounded-md mb-4 border border-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-amber-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tanggal</th>
                                <th scope="col" class="px-6 py-3">Unit Kerja</th>
                                <th scope="col" class="px-6 py-3 text-center">Shift</th>
                                <th scope="col" class="px-6 py-3 text-center">Kehadiran</th>
                                <th scope="col" class="px-6 py-3 text-center">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</td>
                                <td class="px-6 py-4">{{ $report->unit->nama }}</td>
                                <td class="px-6 py-4 text-center">{{ $report->shift }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-green-600 font-bold">{{ $report->jml_hadir }}</span> / {{ $report->jml_personil }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($report->status == 'terkirim')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-200">Terkirim</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-200">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <!-- Tombol Detail yang sudah disambungkan -->
                                    <a href="{{ route('koordinator.safety-talk.show', $report->id) }}" class="text-blue-600 hover:text-blue-800 font-bold underline decoration-2 underline-offset-2 transition">Lihat Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat laporan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
<?php

return [
    // Tanggal mulai aplikasi dipakai (Y-m-d). Tanggal sebelumnya tidak dihitung "belum lapor".
    // Isi dengan tanggal laporan pertama masuk, atau biarkan null jika semua tanggal harus dihitung.
    'tanggal_mulai' => null,

    // Selain hari Minggu, tambahkan tanggal libur nasional/khusus (format Y-m-d)
    'hari_libur' => [
        // '2026-12-25',
    ],

    // Penandatangan pada PDF. Nama dibaca dari scan lembar kertas, mohon dicek ejaannya.
    'penandatangan' => [
        'diketahui'  => ['jabatan' => 'Direktur Operasi', 'nama' => 'Rifki S. Pradipta, S.Kom'],
        'mengetahui' => ['jabatan' => 'Kadiv LGRC',       'nama' => "H. Ma'rang"],
        'dibuat'     => ['jabatan' => 'Kanit K3',         'nama' => 'Haeruddin'],
    ],
];
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Koordinat GPS Sekolah
    |--------------------------------------------------------------------------
    | Isi dengan koordinat GPS lokasi sekolah yang sebenarnya.
    | Gunakan Google Maps untuk mendapatkan koordinat yang akurat.
    */
    'school_lat' => (float) env('SCHOOL_LAT', -7.4040029),
    'school_lng' => (float) env('SCHOOL_LNG', 109.2309305),

    /*
    |--------------------------------------------------------------------------
    | Radius Presensi
    |--------------------------------------------------------------------------
    | Jarak maksimal siswa dari sekolah saat melakukan presensi (dalam meter).
    */
    'max_distance_m' => (int) env('MAX_DISTANCE', 10),

    /*
    |--------------------------------------------------------------------------
    | Akurasi GPS Minimum
    |--------------------------------------------------------------------------
    | Jika akurasi GPS siswa lebih buruk dari nilai ini, presensi ditolak.
    */
    'min_gps_accuracy' => (int) env('MIN_GPS_ACCURACY', 150),

    /*
    |--------------------------------------------------------------------------
    | QR Code
    |--------------------------------------------------------------------------
    */
    'qr_secret_key'       => env('QR_SECRET', 'ganti-dengan-string-random-64-karakter'),
    'qr_refresh_minutes'  => (int) env('QR_REFRESH_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Masa Berlaku Token API Mahasiswa
    |--------------------------------------------------------------------------
    | Token API tidak lagi berlaku selamanya. Setelah TTL ini token akan
    | ditolak oleh middleware AuthStudent dan mahasiswa harus login ulang.
    */
    'api_token_ttl_days' => (int) env('API_TOKEN_TTL_DAYS', 30),
];

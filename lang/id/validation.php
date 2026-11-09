<?php

return [
    'accepted' => ':attribute harus disetujui.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berisi tanggal setelah :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus berisi tanggal sebelum :date.',
    'between' => [
        'numeric' => ':attribute harus bernilai antara :min sampai :max.',
        'file' => ':attribute harus berukuran antara :min sampai :max kilobytes.',
        'string' => ':attribute harus berisi antara :min sampai :max karakter.',
        'array' => ':attribute harus memiliki antara :min sampai :max item.',
    ],
    'boolean' => ':attribute harus bernilai true atau false.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_format' => ':attribute tidak cocok dengan format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus terdiri dari :digits angka.',
    'digits_between' => ':attribute harus terdiri dari antara :min sampai :max angka.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa sebuah file.',
    'filled' => ':attribute harus memiliki nilai.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'file' => ':attribute maksimal :max kilobytes.',
        'string' => ':attribute maksimal :max karakter.',
        'array' => ':attribute maksimal :max item.',
    ],
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'file' => ':attribute minimal :min kilobytes.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal :min item.',
    ],
    'not_in' => ':attribute yang dipilih tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => 'Kolom :attribute wajib diisi.',
    'required_if' => 'Kolom :attribute wajib diisi bila :other adalah :value.',
    'required_with' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_without' => 'Kolom :attribute wajib diisi bila tidak terdapat :values.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'numeric' => ':attribute harus berukuran :size.',
        'file' => ':attribute harus berukuran :size kilobytes.',
        'string' => ':attribute harus berukuran :size karakter.',
        'array' => ':attribute harus mengandung :size item.',
    ],
    'string' => ':attribute harus berupa string.',
    'unique' => ':attribute sudah digunakan.',
    'url' => 'Format :attribute tidak valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name' => 'Nama Lengkap',
        'email' => 'Email',
        'password' => 'Password',
        'no_telepon' => 'No. Telepon',
        'alamat_pengiriman' => 'Alamat Lengkap',
        'nama_penerima' => 'Nama Penerima',
    ],
];
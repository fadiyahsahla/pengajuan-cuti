<?php
return [
    'flows' => [
        'operator_produksi' => [
            1 => 'kepala_regu',
            2 => 'personalia',
            3 => 'hrd'
        ],
        'operator_mekanik' => [
            1 => 'kepala_bagian_mekanik',
            2 => 'personalia',
            3 => 'hrd'
        ],
        'kepala_regu' => [
            1 => 'kepala_bagian_produksi',
            2 => 'personalia',
            3 => 'hrd'
        ],
        'pengawas' => [
            1 => 'kepala_bagian_produksi',
            2 => 'personalia',
            3 => 'hrd'
        ],
        'kepala_bagian_produksi' => [
            1 => 'personalia',
            2 => 'hrd'
        ],
        'kepala_bagian_mekanik' => [
            1 => 'personalia',
            2 => 'hrd'
        ],
        'personalia' => [
            1 => 'hrd'
        ],
        'admin' => [],
        'asisten_personalia' => [
            1 => 'personalia',
            2 => 'hrd'
        ]
    ],

    'jabatan_mapping' => [
        1 => 'operator_produksi',
        2 => 'operator_mekanik',
        3 => 'kepala_regu',
        4 => 'pengawas',
        5 => 'kepala_bagian_produksi',
        6 => 'kepala_bagian_mekanik',
        7 => 'personalia',
        8 => 'hrd',
        9 => 'admin',
        10 => 'asisten_personalia',
    ],
];

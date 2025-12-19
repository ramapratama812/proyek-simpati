<?php

return [

    /*
     * All models in these directories will be scanned for ER diagram generation.
     * By default, the `app` directory will be scanned recursively for models.
     */
    'directories' => [
        base_path('app' . DIRECTORY_SEPARATOR . 'Models'),
    ],

    /*
     * If you want to ignore complete models or certain relations of a specific model,
     * you can specify them here.
     */
    'ignore' => [
        // User::class,
        // Post::class => [
        //     'user'
        // ]
    ],

    /*
     * If you want to see only specific models, specify them here using fully qualified
     * classnames.
     */
    'whitelist' => [
        // App\User::class,
        // App\Post::class,
    ],

    /*
     * If true, all directories specified will be scanned recursively for models.
     */
    'recursive' => true,

    /*
     * The generator will automatically try to look up the model specific columns
     * and add them to the generated output.
     */
    'use_db_schema' => true,

    /*
     * This setting toggles weather the column types (VARCHAR, INT, TEXT, etc.)
     * should be visible on the generated diagram.
     * SAYA UBAH KE TRUE AGAR LEBIH INFORMATIF, JIKA TERLALU PENUH UBAH KE FALSE.
     */
    'use_column_types' => true,

    /*
     * Colors used in the table representation.
     * SAYA UBAH MENJADI TEMA BIRU/PUTIH AGAR LEBIH KONTRAS DAN BERSIH.
     */
    'table' => [
        'header_background_color' => '#2980b9', // Biru solid
        'header_font_color' => '#ffffff',       // Teks Putih
        'row_background_color' => '#ffffff',    // Latar Putih
        'row_font_color' => '#333333',          // Teks Abu gelap (bacaan enak)
    ],

    /*
     * Graphviz attributes.
     * PENGATURAN TATA LETAK UTAMA.
     */
    'graph' => [
        'style' => 'filled',
        'bgcolor' => '#F4F6F6', // Latar belakang abu sangat muda (tidak menyilaukan)
        'fontsize' => 12,
        'labelloc' => 't',
        'concentrate' => true,  // Menggabungkan garis yang tujuannya sama (mengurangi kusut)
        'splines' => 'ortho',   // Garis siku-siku (rapi)
        'overlap' => false,
        'nodesep' => 1.2,       // Jarak horizontal antar tabel (diperlebar)
        'rankdir' => 'TB',      // Top to Bottom (Lebih mudah dibaca daripada LR)
        'pad' => 0.5,
        'ranksep' => 1.5,       // Jarak vertikal antar level tabel (diperlebar)
        'sep' => '+25',
        'fontname' => 'Arial'   // Font standar yang tegas
    ],

    'node' => [
        'margin' => 0,
        'shape' => 'Mrecord',
        'style' => 'filled, rounded',
        'fontname' => 'Arial'
    ],

    /*
     * Pengaturan Garis Relasi
     */
    'edge' => [
        'color' => '#555555',    // Warna garis abu tua
        'penwidth' => 1.2,       // Ketebalan garis sedikit ditambah
        'style' => 'solid',      // Garis solid (bukan putus-putus) agar lebih jelas
        'fontname' => 'Arial'
    ],

    'relations' => [
        'HasOne' => [
            'dir' => 'both',
            'color' => '#27ae60', // Hijau untuk HasOne (pembeda visual)
            'arrowhead' => 'tee',
            'arrowtail' => 'none',
            'style' => 'solid',
        ],
        'BelongsTo' => [
            'dir' => 'both',
            'color' => '#555555',
            'arrowhead' => 'tee',
            'arrowtail' => 'crow',
            'style' => 'solid',
        ],
        'HasMany' => [
            'dir' => 'both',
            'color' => '#2980b9', // Biru untuk HasMany
            'arrowhead' => 'crow',
            'arrowtail' => 'none',
            'style' => 'solid',
        ],
    ]
];

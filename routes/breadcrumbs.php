<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

/*
|--------------------------------------------------------------------------
| Hazard Breadcrumbs
|--------------------------------------------------------------------------
| Pastikan nama breadcrumb sama dengan nama route agar mudah dipanggil.
| Nama breadcrumb boleh beda dari nama route, tapi sebaiknya konsisten.
*/

// Halaman daftar hazard
Breadcrumbs::for('hazard', function (Trail $trail) {
    // Ganti 'Home' dengan nama lain jika mau
    $trail->push('Hazard List', route('hazard'));
});

// Form create hazard
Breadcrumbs::for('hazard-form', function (Trail $trail) {
    $trail->parent('hazard');
    $trail->push('Create Hazard', route('hazard-form'));
});

// Detail hazard (parameter model/ID)
Breadcrumbs::for('hazard-detail', function (Trail $trail, $hazard) {
    $trail->parent('hazard');
    $trail->push("Detail #{$hazard->id}", route('hazard-detail', $hazard));
});

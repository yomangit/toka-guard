<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

// 1. Halaman daftar hazard
Breadcrumbs::for('hazard', function (Trail $trail) {
    $trail->push('Hazard List', route('hazard')); // bisa diarahkan ke dashboard kalau perlu
});

// 2. Form tambah hazard
Breadcrumbs::for('hazard-form', function (Trail $trail) {
    $trail->parent('hazard');
    $trail->push('Create Hazard', route('hazard-form'));
});

// 3. Detail hazard
Breadcrumbs::for('hazard-detail', function (Trail $trail, $hazard) {
    $trail->parent('hazard');
    $trail->push("Detail #{$hazard->id}", route('hazard-detail', $hazard));
});

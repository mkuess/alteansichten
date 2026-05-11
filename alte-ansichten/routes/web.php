<?php

use App\Models\Municipality;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.home');
});

Route::get('/gemeinden', function () {
    $municipalities = Municipality::with('district')
        ->where('public_profile_enabled', true)
        ->whereNotIn('status', ['hidden', 'archived'])
        ->orderBy('name')
        ->get();

    return view('public.municipalities.index', compact('municipalities'));
});

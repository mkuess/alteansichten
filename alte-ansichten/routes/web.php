<?php

use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\QrRedirectController;
use App\Models\Municipality;
use Illuminate\Support\Facades\Route;

Route::get('/qr/{code}', [QrRedirectController::class, 'redirect'])->name('qr.redirect');

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/media-upload', [MediaUploadController::class, 'store'])->name('admin.media-upload');
});


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

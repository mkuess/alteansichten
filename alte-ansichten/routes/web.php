<?php
use App\Http\Controllers\QrRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/qr/{code}', [QrRedirectController::class, 'redirect'])->name('qr.redirect');

Route::get('/', function () {
    return redirect('/admin');
});

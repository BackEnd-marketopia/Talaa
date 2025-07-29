<?php

use App\Http\Controllers\Admin\InvoiceController;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;


Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/lang/{lang}', function ($lang) {
    session()->put('lang', $lang);
    return redirect()->back();
})->name('lang')->middleware('webLang');

Route::get('/orders/{order}/invoice', [InvoiceController::class, 'show'])
    ->name('admin.orders.invoice');

<?php

use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;

Route::get('', function () {
    return redirect()->route('filament.admin.auth.login');
});

// Route::get('/bapp-preview', function () {
//     return view('pdf.bapp');
// });
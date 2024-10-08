<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\SingleSignOnController::class, 'redirect'])
    ->name('sso.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\SingleSignOnController::class, 'callback'])
    ->name('sso.callback');

Route::get('send-mail-test', function (){
    $details = [
        'title' => 'Mail from Financial Reports By Pengg',
        'body' => 'Selamat datang di Financial Reports By Pengg'
    ];

    \Illuminate\Support\Facades\Mail::to('shafwansyah250@gmail.com')->send(new \App\Mail\MyTestMail($details));
});

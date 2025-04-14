<?php

use App\Http\Controllers\PreReservaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Gate::allows('authorizedUser')) {
        return redirect()->route('list-user-related');
    }
    else{
        return view('welcome');
    }
});

Route::get('/form', [PreReservaController::class, 'form'])->name('form');
Route::post('/form', [PreReservaController::class, 'submission'])->name('submissao');
Route::get('/list-user', [PreReservaController::class, 'listUser'])->name('list-user');
Route::get('/list-user-related', [PreReservaController::class, 'listUserRelated'])->name('list-user-related');
Route::get('/list-all', [PreReservaController::class, 'listAll'])->name('list-all');
Route::get('/form/{id}/show', [PreReservaController::class, 'showSubmission'])->name('form.show');
Route::get('/form/{id}/edit', [PreReservaController::class, 'editSubmission'])->name('form.edit');
Route::post('/form/{id}/edit', [PreReservaController::class, 'updateSubmission'])->name('form.update');
Route::delete('/form/{id}', [PreReservaController::class, 'deleteSubmission'])->name('form.delete');
Route::post('/form/{id}/accept', [PreReservaController::class, 'accept'])->name('form.accept');

// Permite usar Gate::check('user')na view 404
Route::fallback(function(){
    return view('errors.404');
 });

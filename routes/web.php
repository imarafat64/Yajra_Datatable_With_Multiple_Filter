<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('users',[UserController::class,'index'])->name('users.index');
Route::controller(UserController::class)->group(function(){
    Route::get('users','index')->name('users.index');
    Route::get('users/edit/{id}', 'edit')->name('users.edit');
    Route::delete('users/delete/{id}','destroy')->name('users.destroy');
    Route::put('users/update','update')->name('users.update');
  });
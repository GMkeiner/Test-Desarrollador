<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\VentaController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('clientes', ClienteController::class);
Route::resource('direcciones', DireccionController::class);
Route::resource('ventas', VentaController::class);
Route::put('/clientes/{id}', [ClienteController::class, 'update']);
Route::put('/direcciones/{id}', [DireccionController::class, 'update'])->name('direcciones.update');
Route::put('/ventas/{id}', [VentaController::class, 'update'])->name('ventas.update');
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');



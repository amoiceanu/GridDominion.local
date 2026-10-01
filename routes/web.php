<?php

use App\Http\Controllers\Game\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('game');
Route::prefix('games/{game}')->group(function (): void {
    Route::get('state', [GameController::class, 'state']);
    Route::post('tick', [GameController::class, 'tick']);
    Route::post('speed', [GameController::class, 'speed']);
    Route::post('build', [GameController::class, 'build']);
});

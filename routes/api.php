<?php

declare(strict_types=1);

use App\Http\Controllers\v1\ExchangeController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['api.key', 'api.check.support.method'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::match([Request::METHOD_GET, Request::METHOD_POST], '/', ExchangeController::class);
    });
});

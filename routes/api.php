<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\TelegramController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// //Bütün markaları gətir (+)
// Route::get('/brands', [BrandController::class, 'index']);

// // 1 marka göster (+)
// Route::get('/brands/{id}', [BrandController::class, 'show']);

// // Yeni marka əlavə et (+)
// Route::post('/brands', [BrandController::class, 'store']);

// // Marka yenilə (+)
// Route::put('/brands/{id}', [BrandController::class, 'update']);
Route::get('/brands-test', [BrandController::class, 'test']);

//brand resource (+)
Route::resource('brands', BrandController::class)->except(['destroy']);

//brand list (id, title) (+)
Route::get('/brands-list', [BrandController::class, 'list']);

//status change (+)
Route::put('/brands/status-change/{id}', [BrandController::class, 'statusChange']);

//brand single view (id, title) (+)
Route::get('/brands-single-view', [BrandController::class, 'singleView']);

//filter by (+)
Route::get('/brands-filter-by', [BrandController::class, 'filterBy']);


//model resource (+)
Route::resource('models', ModelController::class);

//brand list (id, title) (+)
Route::get('/models-list', [ModelController::class, 'list']);

//status change (+)
Route::put('/models/status-change/{id}', [ModelController::class, 'statusChange']);

//brand single view (id, title) (+)
Route::get('/models-single-view', [ModelController::class, 'singleView']);

//filter by (+)
Route::get('/models-filter-by', [ModelController::class, 'filterBy']);

Route::post('/telegram-webhook', [TelegramController::class, 'handleTelegramUpdates']);

//tip
//neqliyyat vasitesi qruplari
//neqliyyat vasitesi alt qruplari
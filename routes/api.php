<?php

use App\Http\Controllers\API\DashboedController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);


Route::get('test', function () {
    return response()->json('test');
});


Route::middleware('auth:api')->group( function () {



Route::get('dashboard', [DashboedController::class,'dashboard']);

Route::post('list_engin', [DashboedController::class,'list_engin']);

Route::get('famille_engin', [DashboedController::class,'famille_engin']);


Route::post('Ajouter_demande', [DashboedController::class,'Ajouter_demande']);

Route::post('consulter_demandes', [DashboedController::class,'consulter_demandes']);

Route::post('detail_demande', [DashboedController::class,'detail_demande']);

Route::post('Engin', [DashboedController::class,'Engin']);

Route::get('getAllDemandes', [DashboedController::class,'getAllDemandes']);

Route::post('addDetailEnjin', [DashboedController::class,'addDetailEnjin']);

Route::post('getDetailCritaires', [DashboedController::class,'getDetailCritaires']);

Route::post('createControles', [DashboedController::class,'createControles']);

Route::post('details_affectation', [DashboedController::class,'details_affectation']);

Route::get('Historique_affectation', [DashboedController::class,'Historique_affectation']);


});

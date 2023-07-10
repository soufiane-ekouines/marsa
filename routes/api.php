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

    Route::post('logout', [RegisterController::class, 'logout']);


Route::get('dashboard', [DashboedController::class,'dashboard']);

Route::post('list_engin', [DashboedController::class,'list_engin']);

Route::get('famille_engin', [DashboedController::class,'famille_engin']);

Route::get('users', [DashboedController::class,'users']);
Route::get('entites', [DashboedController::class,'entites']);


Route::post('/delete_demande', [DashboedController::class, 'delete_demande']);
Route::post('/delete_detail_demande', [DashboedController::class, 'delete_dettail_demande']);
Route::post('/delete_detail_engin', [DashboedController::class, 'delete_dettail_engin']);


Route::post('Ajouter_demande', [DashboedController::class,'Ajouter_demande']);

Route::post('consulter_demandes', [DashboedController::class,'consulter_demandes']);

Route::post('detail_demande', [DashboedController::class,'detail_demande']);

Route::post('Engin', [DashboedController::class,'Engin']);

Route::get('getAllDemandes', [DashboedController::class,'getAllDemandes']);

Route::post('addDetailEnjin', [DashboedController::class,'addDetailEnjin']);

Route::post('getDetailCritaires', [DashboedController::class,'getDetailCritaires']);

Route::post('createControles', [DashboedController::class,'createControles']);

Route::post('details_affectation', [DashboedController::class,'details_affectation']);

Route::post('affectation', [DashboedController::class,'affectation']);
Route::post('sortie', [DashboedController::class,'sortie']);
Route::post('entrer', [DashboedController::class,'entrer']);


Route::post('search_affectation', [DashboedController::class,'search_affectation']);


Route::get('Historique_affectation', [DashboedController::class,'Historique_affectation']);




});

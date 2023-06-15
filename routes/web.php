<?php

use App\Http\Controllers\TrengkasController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});
Route::get('export',[TrengkasController::class,'export']);
// Route::group(['prefix'=>'admin'],function(){

// })

// Route::get('trengkas/semak',[TrengkasController::class,'semak']);
Route::post('trengkas/semak',[TrengkasController::class,'semak']);
Route::get('transparent',[TrengkasController::class,'transparentImage']);

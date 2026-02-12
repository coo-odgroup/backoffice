<?php
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CitiesController;

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
    return view('welcome');
});

Route::prefix('admin')->group(function () {

    // DEFAULT ADMIN PAGE
    Route::get('/', [ModuleController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/cities', [CitiesController::class, 'cities']);
    Route::get('/add-cities', [CitiesController::class, 'addCities']);
    Route::post('cities/datatable', [CitiesController::class, 'dataTable'])
    ->name('cities.datatable');
    Route::post('cities/edit/{id}', [CitiesController::class, 'edit'])
    ->name('cities.edit');


});

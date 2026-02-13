<?php
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CitiesController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Master\BoardingDroppingController;
use App\Models\States;
use App\Http\Controllers\Master\StateController;
use App\Http\Controllers\Master\DistrictController;
use App\Http\Controllers\Master\SeatingTypeController;
use App\Http\Controllers\Master\AmenitiesController;
use App\Http\Controllers\Master\BusTypeController;

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
    Route::post('cities/dataTableView', [CitiesController::class, 'dataTableView'])->name('cities.dataTableView');
    Route::post('cities/edit/{id}', [CitiesController::class, 'edit'])->name('cities.edit');
    Route::post('/get-state-list', [CommonController::class, 'getStateList'])->name('get.state.list');
    Route::post('/get-district-list', [CommonController::class, 'getDistrictList'])->name('get.district.list');

    Route::get('/boardingDropping', [BoardingDroppingController::class, 'boardingDropping'])->name('boarding.dropping');
    Route::get('/states', [StateController::class, 'states'])->name('states');
    Route::get('/district', [DistrictController::class, 'district'])->name('district');
    Route::get('/seating-type', [SeatingTypeController::class, 'seatingType'])->name('seating.type');
    Route::get('/bus-type', [BusTypeController::class, 'bustype'])->name('bus.type');
    Route::get('/amenities', [AmenitiesController::class, 'amenities'])->name('amenities');

});
        
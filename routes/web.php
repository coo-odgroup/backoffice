<?php
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CitiesController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Master\BoardingDroppingController;
use App\Models\Master\States;
use App\Http\Controllers\Master\StateController;
use App\Http\Controllers\Master\DistrictController;
use App\Http\Controllers\Master\SeatingTypeController;
use App\Http\Controllers\Master\AmenitiesController;
use App\Http\Controllers\Master\BusTypeController;
use App\Http\Controllers\Master\AmenityCategoryController;
use App\Http\Controllers\Master\ApiAppsController;
use App\Http\Controllers\Master\ApikeysController;

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
    
    Route::post('/get-state-list', [CommonController::class, 'getStateList'])->name('get.state.list');
    Route::post('/get-district-list', [CommonController::class, 'getDistrictList'])->name('get.district.list');
    Route::post('/common-bulk-action', [CommonController::class, 'bulkAction'])->name('admin.bulkAction');
    Route::post('/audit-logs', [CommonController::class, 'getLogs'])->name('admin.getLogs');
    Route::post('/update-sequence', [CommonController::class, 'updateSequence'])->name('common.updateSequence');
    Route::post('/get-amenity-category-list', [CommonController::class, 'getAmenityCategoryList'])->name('get.amenity.category.list');
    Route::post('/get-apiapps-list', [CommonController::class, 'getApiAppsList'])->name('getapiapps.list');

    Route::get('/cities', [CitiesController::class, 'cities'])->name('cities.index');
    Route::match(['get', 'post'], 'cities/add', [CitiesController::class, 'add'])->name('cities.add');
    Route::post('cities/dataTableView', [CitiesController::class, 'dataTableView'])->name('cities.dataTableView');
    Route::match(['get', 'post'], 'cities/edit/{encId}', [CitiesController::class, 'edit'])->name('cities.edit');



    //Subhasis
    Route::get('/boardingDropping', [BoardingDroppingController::class, 'boardingDropping'])->name('boardingDropping.index');
    Route::match(['get', 'post'], 'boardingDropping/add',[BoardingDroppingController::class, 'add'])->name('boardingDropping.add');
    Route::post('boardingDropping/dataTableView', [BoardingDroppingController::class, 'dataTableView'])->name('boardingDropping.dataTableView');
    Route::match(['get', 'post'], 'boardingDropping/edit/{encId}',[BoardingDroppingController::class, 'edit'])->name('boardingDropping.edit');
    Route::post('/get-city-list', [CommonController::class, 'getCityList'])->name('get.city.list');
    Route::post('admin/boardingDropping/check-exists',[BoardingDroppingController::class, 'checkExists'])->name('boardingDropping.checkExists');



    Route::get('/seating-type', [SeatingTypeController::class, 'seatingType'])->name('seating.type');








    // Jagan
    // ---------------------------------------------------------------------------------------------------------------
    // State
    Route::get('/states', [StateController::class, 'states'])->name('states.index');
    Route::match(['get', 'post'], 'states/add', [StateController::class, 'add'])->name('states.add');
    Route::post('states/dataTableView', [StateController::class, 'dataTableView'])->name('states.dataTableView');
    Route::match(['get', 'post'], 'states/edit/{encId}', [StateController::class, 'edit'])->name('states.edit');

    // Bus Type
    Route::get('/bustype', [BusTypeController::class, 'busType'])->name('bustype.index');
    Route::match(['get', 'post'], 'bustype/add', [BusTypeController::class, 'add'])->name('bustype.add');
    Route::post('bustype/dataTableView', [BusTypeController::class, 'dataTableView'])->name('bustype.dataTableView');
    Route::match(['get', 'post'], 'bustype/edit/{encId}', [BusTypeController::class, 'edit'])->name('bustype.edit');

    // Amenity Category
    Route::get('/amenitycategory', [AmenityCategoryController::class, 'amenityCategory'])->name('amenitycategory.index');
    Route::match(['get', 'post'], 'amenitycategory/add', [AmenityCategoryController::class, 'add'])->name('amenitycategory.add');
    Route::post('amenitycategory/dataTableView', [AmenityCategoryController::class, 'dataTableView'])->name('amenitycategory.dataTableView');
    Route::match(['get', 'post'], 'amenitycategory/edit/{encId}', [AmenityCategoryController::class, 'edit'])->name('amenitycategory.edit');

    // Amenities
    Route::get('/amenities', [AmenitiesController::class, 'amenities'])->name('amenities.index');
    Route::match(['get', 'post'], 'amenities/add', [AmenitiesController::class, 'add'])->name('amenities.add');
    Route::post('amenities/dataTableView', [AmenitiesController::class, 'dataTableView'])->name('amenities.dataTableView');
    Route::match(['get', 'post'], 'amenities/edit/{encId}', [AmenitiesController::class, 'edit'])->name('amenities.edit');

    // API App
    Route::get('/apiapps', [ApiAppsController::class, 'apiApps'])->name('apiapps.index');
    Route::match(['get', 'post'], 'apiapps/add', [ApiAppsController::class, 'add'])->name('apiapps.add');
    Route::post('apiapps/dataTableView', [ApiAppsController::class, 'dataTableView'])->name('apiapps.dataTableView');
    Route::match(['get', 'post'], 'apiapps/edit/{encId}', [ApiAppsController::class, 'edit'])->name('apiapps.edit');

    // API Keys
    Route::get('/apikeys', [ApikeysController::class, 'apiKeys'])->name('apikeys.index');
    Route::match(['get', 'post'], 'apikeys/add', [ApikeysController::class, 'add'])->name('apikeys.add');
    Route::post('apikeys/dataTableView', [ApikeysController::class, 'dataTableView'])->name('apikeys.dataTableView');
    Route::match(['get', 'post'], 'apikeys/edit/{encId}', [ApikeysController::class, 'edit'])->name('apikeys.edit');

    // ---------------------------------------------------------------------------------------------------------------




    //Add by sahil
    // ----------------------------------------------------------------------------------------------------------------
    // Districts module
    Route::get('/district', [DistrictController::class, 'district'])->name('district.index');
    Route::match(['get', 'post'], 'district/add', [DistrictController::class, 'add'])->name('district.add');
    Route::post('district/dataTableView', [DistrictController::class, 'dataTableView'])->name('district.dataTableView');
    Route::match(['get', 'post'], 'district/edit/{encId}', [DistrictController::class, 'edit'])->name('district.edit');

    //seating type module
    Route::get('/seatingtype', [SeatingTypeController::class, 'seatingType'])->name('seatingtype.index');
    Route::match(['get', 'post'], 'seatingtype/add', [SeatingTypeController::class, 'add'])->name('seatingtype.add');
    Route::post('seatingtype/dataTableView', [SeatingTypeController::class, 'dataTableView'])->name('seatingtype.dataTableView');
    Route::match(['get', 'post'], 'seatingtype/edit/{encId}', [SeatingTypeController::class, 'edit'])->name('seatingtype.edit');

    //--------------------------------------------------------------------------------------------------------------------

});

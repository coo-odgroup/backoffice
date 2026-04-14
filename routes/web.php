<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CitiesController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Master\BoardingDroppingController;
use App\Http\Controllers\Master\StateController;
use App\Http\Controllers\Master\DistrictController;
use App\Http\Controllers\Master\SeatingTypeController;
use App\Http\Controllers\Master\AmenitiesController;
use App\Http\Controllers\Master\BusTypeController;
use App\Http\Controllers\Master\AmenityCategoryController;
use App\Http\Controllers\Master\ApiAppsController;
use App\Http\Controllers\Master\ApikeysController;
use App\Http\Controllers\Master\RolesController;
use App\Http\Controllers\Master\ReasonController;
use App\Http\Controllers\Master\FestiveDaysController;
use App\Http\Controllers\Master\ModulesController;
use App\Http\Controllers\Master\SeatLayoutController;
use App\Http\Controllers\Master\CityApisController;
use App\Http\Controllers\Master\FaqCategoryController;
use App\Http\Controllers\Master\FaqController;
use App\Http\Controllers\Admin\Blog\BlogCategoryController;
use App\Http\Controllers\Admin\Blog\BlogController;
use App\Http\Controllers\Admin\Ad\VendorController;
use App\Http\Controllers\Admin\Ad\AdPlacementController;
use App\Http\Controllers\Admin\Ad\AdCampaignController;
use App\Http\Controllers\Admin\Ad\PricingPlanController;
use App\Http\Controllers\Master\BrandController;
use App\Http\Controllers\Master\BusModelController;
use App\Http\Controllers\Admin\Ad\AdsController;
use App\Http\Controllers\Admin\Blog\BlogImagesController;
use App\Http\Controllers\Admin\Blog\BlogRoutesController;
use App\Http\Controllers\Admin\Blog\BlogTagMapController;
use App\Http\Controllers\Admin\Blog\BlogTagsController;
use App\Http\Controllers\Admin\Blog\BlogPreviewController;
use App\Http\Controllers\Admin\Bus\BusAmenitiesController;
use App\Http\Controllers\Admin\Bus\BusSeatLayoutController;
use App\Http\Controllers\Admin\Campaign\CampaignMasterController;
use App\Http\Controllers\Admin\Campaign\CampaignController;
use App\Http\Controllers\Master\BusInfoController;
use App\Http\Controllers\Master\ReviewCategoryController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Master\CancellationslabController;
use App\Http\Controllers\Master\CancellationslabInfoController;
use App\Http\Controllers\Master\TicketFareSlabController;
use App\Http\Controllers\Master\TicketFareSlabInfoController;
use App\Http\Controllers\MasterLogController;
use App\Http\Controllers\Master\AxleTypeController;
use App\Http\Controllers\Master\MstSeatLayoutController;
use App\Http\Controllers\Master\BusServiceController;
use App\Http\Controllers\Master\AnnextureTypeController;
use App\Http\Controllers\Master\AnnextureController;
use App\Http\Controllers\Admin\Bus\BusWizardController;
use App\Http\Controllers\Master\BusScheduleController;
use App\Http\Controllers\Master\BusCancelController;
use App\Http\Controllers\Master\SeatBlockController;
use App\Http\Controllers\Master\ExtraSeatBlockController;
use App\Http\Controllers\Master\SeatOpenController;
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

Route::prefix('admin/bus/create')->group(function () {

    Route::get('/step1/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step1'])->name('bus.step1');
    Route::post('/step1/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep1']);

    Route::get('/step2/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step2'])->name('bus.step2');
    Route::post('/step2/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep2']);

    Route::get('/step3/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step3'])->name('bus.step3');
    Route::post('/step3/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep3']);

    Route::get('/step4/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step4'])->name('bus.step4');
    Route::post('/step4/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep4']);

    Route::get('/step5/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step5'])->name('bus.step5');
    Route::post('/step5/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep5']);

    Route::get('/step6/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step6'])->name('bus.step6');
    Route::post('/step6/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep6']);

    Route::get('/step7/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'step7'])->name('bus.step7');
    Route::post('/step7/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'postStep7']);

    Route::get('/preview/{encId?}/{param?}/{param2?}', [BusWizardController::class, 'preview'])->name('bus.preview');
});

Route::prefix('admin')->group(function () {

    // DEFAULT ADMIN PAGE
    Route::get('/', [ModuleController::class, 'index'])->name('admin.dashboard');

    Route::post('/get-state-list', [CommonController::class, 'getStateList'])->name('get.state.list');
    Route::post('/get-district-list', [CommonController::class, 'getDistrictList'])->name('get.district.list');
    Route::post('/common-bulk-action', [CommonController::class, 'bulkAction'])->name('admin.bulkAction');
    Route::post('/audit-logs', [CommonController::class, 'getLogs'])->name('admin.getLogs');
    Route::post('/update-sequence', [CommonController::class, 'updateSequence'])->name('common.updateSequence');
    Route::post('/get-amenity-category-list', [CommonController::class, 'getAmenityCategoryList'])->name('get.amenity.category.list');
    Route::post('/get-apiapps-list', [CommonController::class, 'getApiAppsList'])->name('getapiapps.list');
    Route::post('get-parent-module-list', [CommonController::class, 'getParentModuleList'])->name('modules.parent.list');
    Route::post('/get-city-list', [CommonController::class, 'getCityList'])->name('get.city.list');
    Route::post('get-faq-category-list', [CommonController::class, 'getFaqCategoryList']);
    Route::post('/get-role-list', [CommonController::class, 'getRoleList'])->name('get.role.list');
    Route::post('/get-blog-category-list', [CommonController::class, 'getBlogCategoryList'])->name('get.blogcategory.list');
    Route::post('/remove-image', [CommonController::class, 'removeImage'])->name('remove.image');
    Route::post('/get-blog-list', [CommonController::class, 'getBlogList'])->name('getbloglist');
    Route::post('get-placement-list', [CommonController::class, 'getPlacementList']);
    Route::post('get-vendor-list', [CommonController::class, 'getVendorList']);
    Route::post('get-pricing-plan-list', [CommonController::class, 'getPricingPlanList']);
    Route::post('get-campaign-list', [CommonController::class, 'getCampaignList']);
    Route::post('get-country-list', [CommonController::class, 'getCountryList']);
    Route::post('get-brand-list', [CommonController::class, 'getBrandList'])->name('common.getBrandList');
    Route::post('get-blogtags-list', [CommonController::class, 'getBlogTagsList']);
    Route::post('get-cancellationslab-list', [CommonController::class, 'getCancellationslabList']);
    Route::post('get-annexure-type-list', [CommonController::class, 'getAnnexureTypeList']);
    Route::post('get-campaign-master-list', [CommonController::class, 'getCampaignMasterList']);
    Route::post('get-busoperator-list', [CommonController::class, 'getBusOperatorList']);
    Route::post('get-annexture-list', [CommonController::class, 'getAnnextureList']);
    Route::get('get-boarding-dropping', [BusWizardController::class, 'getBoardingDropping']);
    Route::post('get-listing-time', [BusWizardController::class, 'getListingTime']);
    Route::post('get-boarding-dropping-by-cityId', [BoardingDroppingController::class, 'getBoardingDroppingByCityId']);
    Route::get('/get-seats-by-layout', [BusWizardController::class, 'getSeatsByLayout']);
    Route::get('ticket-fare-slab/list', [CommonController::class, 'getTicketFareSlabList'])->name('ticketfareslab.list');
    Route::post('get-busoperator-role-list', [CommonController::class, 'getBusOperatorListRoleWise']);
    Route::post('get-buses-by-operator', [CommonController::class, 'getBusesByOperator']);
    Route::post('get-schedule-dates', [BusScheduleController::class, 'getScheduleDates']);
    Route::post('get-users-list', [CommonController::class, 'getUsersList']);
    Route::post('get-bus-cancel-reasons', [CommonController::class, 'getBusCancelReasons']);

    // Common Bus Info
    Route::post('get-busmodels-list', [CommonController::class, 'getBusModelsList']);
    Route::post('get-axletype-list', [CommonController::class, 'getAxleTypeList']);
    Route::post('get-busservices-list', [CommonController::class, 'getBusServicesList']);
    Route::post('get-seattype-list', [CommonController::class, 'getSeatTypeList']);
    Route::post('get-seatlayout-list', [CommonController::class, 'getSeatLayoutList']);

    Route::get('/cities', [CitiesController::class, 'cities'])->name('cities.index');
    Route::match(['get', 'post'], 'cities/add', [CitiesController::class, 'add'])->name('cities.add');
    Route::post('cities/dataTableView', [CitiesController::class, 'dataTableView'])->name('cities.dataTableView');
    Route::match(['get', 'post'], 'cities/edit/{encId}', [CitiesController::class, 'edit'])->name('cities.edit');

    Route::get('/seat-layout', [SeatLayoutController::class, 'index'])->name('seatlayout.index');
    Route::match(['get', 'post'], 'seat-layout/add', [SeatLayoutController::class, 'add'])->name('seatlayout.add');
    Route::match(['get', 'post'], 'seat-layout/edit/{encId}', [SeatLayoutController::class, 'edit'])->name('seatlayout.edit');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-log.index');

    Route::post('/get-amenity-list', [CommonController::class, 'getAmenities']);

    Route::get('search-amenities', [CommonController::class, 'searchAmenities']);
    Route::get('get-slab-details', [CommonController::class, 'getSlabDetails']);

    Route::post('/admin/upload-editor-image', [BlogController::class, 'uploadEditorImage']);

    //Subhasis
    //___________________________________________________________________________________________________________________________________________________

    //cities
    Route::get('/cities', [CitiesController::class, 'cities'])->name('cities.index');
    Route::match(['get', 'post'], 'cities/add', [CitiesController::class, 'add'])->name('cities.add');
    Route::post('cities/dataTableView', [CitiesController::class, 'dataTableView'])->name('cities.dataTableView');
    Route::match(['get', 'post'], 'cities/edit/{encId}', [CitiesController::class, 'edit'])->name('cities.edit');

    // Boarding & Dropping Points
    Route::get('/boardingDropping', [BoardingDroppingController::class, 'boardingDropping'])->name('boardingDropping.index');
    Route::match(['get', 'post'], 'boardingDropping/add', [BoardingDroppingController::class, 'add'])->name('boardingDropping.add');
    Route::post('boardingDropping/dataTableView', [BoardingDroppingController::class, 'dataTableView'])->name('boardingDropping.dataTableView');
    Route::match(['get', 'post'], 'boardingDropping/edit/{encId}', [BoardingDroppingController::class, 'edit'])->name('boardingDropping.edit');
    Route::post('admin/boardingDropping/check-exists', [BoardingDroppingController::class, 'checkExists'])->name('boardingDropping.checkExists');

    // Roles
    Route::get('/roles', [RolesController::class, 'roles'])->name('roles.index');
    Route::match(['get', 'post'], 'roles/add', [RolesController::class, 'add'])->name('roles.add');
    Route::post('roles/dataTableView', [RolesController::class, 'dataTableView'])->name('roles.dataTableView');
    Route::match(['get', 'post'], 'roles/edit/{encId}', [RolesController::class, 'edit'])->name('roles.edit');
    Route::post('admin/roles/check-exists', [RolesController::class, 'checkExists'])->name('roles.checkExists');

    // Modules
    Route::get('/modules', [ModulesController::class, 'modules'])->name('modules.index');
    Route::match(['get', 'post'], 'modules/add', [ModulesController::class, 'add'])->name('modules.add');
    Route::post('modules/dataTableView', [ModulesController::class, 'dataTableView'])->name('modules.dataTableView');
    Route::match(['get', 'post'], 'modules/edit/{encId}', [ModulesController::class, 'edit'])->name('modules.edit');
    Route::post('admin/modules/check-exists', [ModulesController::class, 'checkExists'])->name('modules.checkExists');

    //FAQ Catagory
    Route::get('/faqcategory', [FaqCategoryController::class, 'faqCategory'])->name('faqcategory.index');
    Route::match(['get', 'post'], 'faqcategory/add', [FaqCategoryController::class, 'add'])->name('faqcategory.add');
    Route::post('faqcategory/dataTableView', [FaqCategoryController::class, 'dataTableView'])->name('faqcategory.dataTableView');
    Route::match(['get', 'post'], 'faqcategory/edit/{encId}', [FaqCategoryController::class, 'edit'])->name('faqcategory.edit');

    // FAQs
    Route::get('/faq', [FaqController::class, 'faq'])->name('faq.index');
    Route::match(['get', 'post'], 'faq/add', [FaqController::class, 'add'])->name('faq.add');
    Route::post('faq/dataTableView', [FaqController::class, 'dataTableView'])->name('faq.dataTableView');
    Route::match(['get', 'post'], 'faq/edit/{encId}', [FaqController::class, 'edit'])->name('faq.edit');

    //Vendors
    Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::match(['get', 'post'], 'vendor/add', [VendorController::class, 'add'])->name('vendor.add');
    Route::post('vendor/dataTableView', [VendorController::class, 'dataTableView'])->name('vendor.dataTableView');
    Route::match(['get', 'post'], 'vendor/edit/{encId}', [VendorController::class, 'edit'])->name('vendor.edit');
    Route::post('admin/Ad/vendor/check-exists', [VendorController::class, 'checkExists'])->name('vendor.checkExists');

    //Ad Placements
    Route::get('/ad-placement', [AdPlacementController::class, 'index'])->name('AdPlacement.index');
    Route::match(['get', 'post'], 'ad-placement/add', [AdPlacementController::class, 'add'])->name('AdPlacement.add');
    Route::post('ad-placement/dataTableView', [AdPlacementController::class, 'dataTableView'])->name('AdPlacement.dataTableView');
    Route::match(['get', 'post'], 'ad-placement/edit/{encId}', [AdPlacementController::class, 'edit'])->name('AdPlacement.edit');
    Route::post('ad-placement/check-exists', [AdPlacementController::class, 'checkExists'])->name('AdPlacement.checkExists');

    // Reason
    Route::get('/reason', [ReasonController::class, 'reason'])->name('reason.index');
    Route::match(['get', 'post'], 'reason/add', [ReasonController::class, 'add'])->name('reason.add');
    Route::post('reason/dataTableView', [ReasonController::class, 'dataTableView'])->name('reason.dataTableView');
    Route::match(['get', 'post'], 'reason/edit/{encId}', [ReasonController::class, 'edit'])->name('reason.edit');
    Route::post('admin/reason/check-exists', [ReasonController::class, 'checkExists'])->name('reason.checkExists');

    // Pricing Plan
    Route::get('/pricing-plan', [PricingPlanController::class, 'index'])->name('pricingPlan.index');
    Route::match(['get', 'post'], 'pricing-plan/add', [PricingPlanController::class, 'add'])->name('pricingPlan.add');
    Route::post('pricing-plan/dataTableView', [PricingPlanController::class, 'dataTableView'])->name('pricingPlan.dataTableView');
    Route::match(['get', 'post'], 'pricing-plan/edit/{encId}', [PricingPlanController::class, 'edit'])->name('pricingPlan.edit');
    Route::post('admin/pricing-plan/check-exists', [PricingPlanController::class, 'checkExists'])->name('pricingPlan.checkExists');

    //Ad Campaign
    Route::get('/ad-campaign', [AdCampaignController::class, 'index'])->name('AdCampaign.index');
    Route::match(['get', 'post'], 'ad-campaign/add', [AdCampaignController::class, 'add'])->name('AdCampaign.add');
    Route::post('ad-campaign/dataTableView', [AdCampaignController::class, 'dataTableView'])->name('AdCampaign.dataTableView');
    Route::match(['get', 'post'], 'ad-campaign/edit/{encId}', [AdCampaignController::class, 'edit'])->name('AdCampaign.edit');
    Route::post('ad-campaign/check-exists', [AdCampaignController::class, 'checkExists'])->name('AdCampaign.checkExists');

    //Ads
    Route::get('/ads', [AdsController::class, 'index'])->name('Ads.index');
    Route::match(['get', 'post'], 'ads/add', [AdsController::class, 'add'])->name('Ads.add');
    Route::post('ads/dataTableView', [AdsController::class, 'dataTableView'])->name('Ads.dataTableView');
    Route::match(['get', 'post'], 'ads/edit/{encId}', [AdsController::class, 'edit'])->name('Ads.edit');
    Route::post('ads/check-exists', [AdsController::class, 'checkExists'])->name('Ads.checkExists');

    //Bus Brand
    Route::get('/brand', [BrandController::class, 'brand'])->name('brand.index');
    Route::match(['get', 'post'], 'brand/add', [BrandController::class, 'add'])->name('brand.add');
    Route::post('brand/dataTableView', [BrandController::class, 'dataTableView'])->name('brand.dataTableView');
    Route::match(['get', 'post'], 'brand/edit/{encId}', [BrandController::class, 'edit'])->name('brand.edit');

    //Bus Model
    Route::get('/bus-model', [BusModelController::class, 'busModel'])->name('busModel.index');
    Route::match(['get', 'post'], 'bus-model/add', [BusModelController::class, 'add'])->name('busModel.add');
    Route::post('bus-model/dataTableView', [BusModelController::class, 'dataTableView'])->name('busModel.dataTableView');
    Route::match(['get', 'post'], 'bus-model/edit/{encId}', [BusModelController::class, 'edit'])->name('busModel.edit');

    //Axle Type
    Route::get('/axle-type', [AxleTypeController::class, 'axleType'])->name('axleType.index');
    Route::match(['get', 'post'], 'axle-type/add', [AxleTypeController::class, 'add'])->name('axleType.add');
    Route::post('axle-type/dataTableView', [AxleTypeController::class, 'dataTableView'])->name('axleType.dataTableView');
    Route::match(['get', 'post'], 'axle-type/edit/{encId}', [AxleTypeController::class, 'edit'])->name('axleType.edit');

    //Bus Service
    Route::get('/bus-service', [BusServiceController::class, 'busService'])->name('busService.index');
    Route::match(['get', 'post'], 'bus-service/add', [BusServiceController::class, 'add'])->name('busService.add');
    Route::post('bus-service/dataTableView', [BusServiceController::class, 'dataTableView'])->name('busService.dataTableView');
    Route::match(['get', 'post'], 'bus-service/edit/{encId}', [BusServiceController::class, 'edit'])->name('busService.edit');

    //Seat Layout
    Route::get('/mst-seatlayout', [MstSeatLayoutController::class, 'mstSeatLayout'])->name('mstSeatLayout.index');
    Route::match(['get', 'post'], 'mst-seatlayout/add', [MstSeatLayoutController::class, 'add'])->name('mstSeatLayout.add');
    Route::post('mst-seatlayout/dataTableView', [MstSeatLayoutController::class, 'dataTableView'])->name('mstSeatLayout.dataTableView');
    Route::match(['get', 'post'], 'mst-seatlayout/edit/{encId}', [MstSeatLayoutController::class, 'edit'])->name('mstSeatLayout.edit');

    //Annexture Type
    Route::get('/annexture-type', [AnnextureTypeController::class, 'annextureType'])->name('annextureType.index');
    Route::match(['get', 'post'], 'annexture-type/add', [AnnextureTypeController::class, 'add'])->name('annextureType.add');
    Route::post('annexture-type/dataTableView', [AnnextureTypeController::class, 'dataTableView'])->name('annextureType.dataTableView');
    Route::match(['get', 'post'], 'annexture-type/edit/{encId}', [AnnextureTypeController::class, 'edit'])->name('annextureType.edit');

    //Annexture
    Route::get('/annexture', [AnnextureController::class, 'annexture'])->name('annexture.index');
    Route::match(['get', 'post'], 'annexture/add', [AnnextureController::class, 'add'])->name('annexture.add');
    Route::post('annexture/dataTableView', [AnnextureController::class, 'dataTableView'])->name('annexture.dataTableView');
    Route::match(['get', 'post'], 'annexture/edit/{encId}', [AnnextureController::class, 'edit'])->name('annexture.edit');
    Route::post('admin/annexture/check-exists', [AnnextureController::class, 'checkExists'])->name('annexture.checkExists');
    Route::get('annexture/get-by-type', [AnnextureController::class, 'getByType'])->name('annexture.getByType');

    // Blogs Preview
    Route::get('/blogs-preview', [BlogPreviewController::class, 'blogPreview'])->name('blogs-preview.index');
    Route::get('/businfo/blog-preview', [BusInfoController::class, 'blogPreview'])->name('businfo.blogPreview');
    Route::get('/admin/blogs-preview/{id}', [BlogPreviewController::class, 'blogPreview'])->name('blogs.preview');
    Route::post('/blogs/update-status', [BlogPreviewController::class, 'updateStatus'])->name('blogs.updateStatus');
    //Festive Days
    Route::get('/festive-days', [FestiveDaysController::class, 'festiveDays'])->name('festiveDays.index');
    Route::match(['get', 'post'], 'festive-days/add', [FestiveDaysController::class, 'add'])->name('festiveDays.add');
    Route::post('festive-days/dataTableView', [FestiveDaysController::class, 'dataTableView'])->name('festiveDays.dataTableView');
    Route::match(['get', 'post'], 'festive-days/edit/{encId}', [FestiveDaysController::class, 'edit'])->name('festiveDays.edit');
    Route::post('admin/festive-days/check-exists', [FestiveDaysController::class, 'checkExists'])->name('festiveDays.checkExists');

    //Ticket Fare Slab
    Route::get('/ticket-fare-slab', [TicketFareSlabController::class, 'index'])->name('ticketfareslab.index');
    Route::match(['get', 'post'], 'ticket-fare-slab/add', [TicketFareSlabController::class, 'add'])->name('ticketfareslab.add');
    Route::post('ticket-fare-slab/dataTableView', [TicketFareSlabController::class, 'dataTableView'])->name('ticketfareslab.dataTableView');
    Route::match(['get', 'post'], 'ticket-fare-slab/edit/{encId}', [TicketFareSlabController::class, 'edit'])->name('ticketfareslab.edit');

    // Ticket Fare Slab Info
    Route::get('/ticketfareslab-info', [TicketFareSlabInfoController::class, 'index'])->name('ticketfareslab-info.index');
    Route::match(['get', 'post'], 'ticketfareslab-info/add', [TicketFareSlabInfoController::class, 'add'])->name('ticketfareslab-info.add');
    Route::post('ticketfareslab-info/dataTableView', [TicketFareSlabInfoController::class, 'dataTableView'])->name('ticketfareslab-info.dataTableView');
    Route::match(['get', 'post'], 'ticketfareslab-info/edit/{encId}', [TicketFareSlabInfoController::class, 'edit'])->name('ticketfareslab-info.edit');
    Route::post('get-operator-slab-data', [TicketFareSlabInfoController::class, 'getOperatorSlabData']);

    // Master Logs
    Route::get('/master-logs', [MasterLogController::class, 'index'])->name('master-log.index');
    Route::get('/admin/audit-logs-data', [MasterLogController::class, 'auditLogData']);
    Route::get('/admin/audit-log/{id}', [MasterLogController::class, 'viewAuditLog']);


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

    // City APIs
    Route::get('/cityapis', [CityApisController::class, 'cityApis'])->name('cityapis.index');
    Route::match(['get', 'post'], 'cityapis/add', [CityApisController::class, 'add'])->name('cityapis.add');
    Route::post('cityapis/dataTableView', [CityApisController::class, 'dataTableView'])->name('cityapis.dataTableView');
    Route::match(['get', 'post'], 'cityapis/edit/{encId}', [CityApisController::class, 'edit'])->name('cityapis.edit');

    // User
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::match(['get', 'post'], 'users/add', [UsersController::class, 'add'])->name('users.add');
    Route::post('users/dataTableView', [UsersController::class, 'dataTableView'])->name('users.dataTableView');
    Route::match(['get', 'post'], 'users/edit/{edit_param}/{encId}', [UsersController::class, 'edit']);
    Route::post('/viewuser', [UsersController::class, 'viewUserRecord'])->name('users.viewuserrecord');

    // Blogs
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::match(['get', 'post'], 'blogs/add', [BlogController::class, 'add'])->name('blogs.add');
    Route::post('blogs/dataTableView', [BlogController::class, 'dataTableView'])->name('blogs.dataTableView');
    Route::match(['get', 'post'], 'blogs/edit/{encId}', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::post('/admin/blogs/publish', [BlogController::class, 'publish'])->name('blogs.publish');

    // Blog Images
    Route::get('/blog-images', [BlogImagesController::class, 'index'])->name('blog-images.index');
    Route::match(['get', 'post'], 'blog-images/add', [BlogImagesController::class, 'add'])->name('blog-images.add');
    Route::post('blog-images/dataTableView', [BlogImagesController::class, 'dataTableView'])->name('blog-images.dataTableView');
    Route::match(['get', 'post'], 'blog-images/edit/{encId}', [BlogImagesController::class, 'edit'])->name('blog-images.edit');
    Route::post('/remove-blog-image', [BlogImagesController::class, 'removeBlogImage']);

    // Blog Routes
    Route::get('/blog-routes', [BlogRoutesController::class, 'index'])->name('blog-routes.index');
    Route::match(['get', 'post'], 'blog-routes/add', [BlogRoutesController::class, 'add'])->name('blog-routes.add');
    Route::post('blog-routes/dataTableView', [BlogRoutesController::class, 'dataTableView'])->name('blog-routes.dataTableView');
    Route::match(['get', 'post'], 'blog-routes/edit/{encId}', [BlogRoutesController::class, 'edit'])->name('blog-routes.edit');

    // Blog Tags
    Route::get('/blog-tags', [BlogTagsController::class, 'index'])->name('blog-tags.index');
    Route::match(['get', 'post'], 'blog-tags/add', [BlogTagsController::class, 'add'])->name('blog-tags.add');
    Route::post('blog-tags/dataTableView', [BlogTagsController::class, 'dataTableView'])->name('blog-tags.dataTableView');
    Route::match(['get', 'post'], 'blog-tags/edit/{encId}', [BlogTagsController::class, 'edit'])->name('blog-tags.edit');

    // Bus Amenities
    Route::get('/bus-amenities', [BusAmenitiesController::class, 'index'])->name('bus-amenities.index');
    Route::match(['get', 'post'], 'bus-amenities/add', [BusAmenitiesController::class, 'add'])->name('bus-amenities.add');
    Route::post('bus-amenities/dataTableView', [BusAmenitiesController::class, 'dataTableView'])->name('bus-amenities.dataTableView');
    Route::match(['get', 'post'], 'bus-amenities/edit/{encId}', [BusAmenitiesController::class, 'edit'])->name('bus-amenities.edit');

    // Cancellation Slab
    Route::get('/cancellationslab', [CancellationslabController::class, 'index'])->name('cancellationslab.index');
    Route::match(['get', 'post'], 'cancellationslab/add', [CancellationslabController::class, 'add'])->name('cancellationslab.add');
    Route::post('cancellationslab/dataTableView', [CancellationslabController::class, 'dataTableView'])->name('cancellationslab.dataTableView');
    Route::match(['get', 'post'], 'cancellationslab/edit/{encId}', [CancellationslabController::class, 'edit'])->name('cancellationslab.edit');

    // Cancellation Slab Info
    Route::get('/cancellationslab-info', [CancellationslabInfoController::class, 'index'])->name('cancellationslab-info.index');
    Route::match(['get', 'post'], 'cancellationslab-info/add', [CancellationslabInfoController::class, 'add'])->name('cancellationslab-info.add');
    Route::post('cancellationslab-info/dataTableView', [CancellationslabInfoController::class, 'dataTableView'])->name('cancellationslab-info.dataTableView');
    Route::match(['get', 'post'], 'cancellationslab-info/edit/{encId}', [CancellationslabInfoController::class, 'edit'])->name('cancellationslab-info.edit');

    // Blog Tag Map
    Route::get('/blog-tag-map', [BlogTagMapController::class, 'index'])->name('blog-tag-map.index');
    Route::match(['get', 'post'], 'blog-tag-map/add', [BlogTagMapController::class, 'add'])->name('blog-tag-map.add');
    Route::post('blog-tag-map/dataTableView', [BlogTagMapController::class, 'dataTableView'])->name('blog-tag-map.dataTableView');
    Route::match(['get', 'post'], 'blog-tag-map/edit/{encId}', [BlogTagMapController::class, 'edit'])->name('blog-tag-map.edit');

    // Campaign Master
    Route::get('/campaign-master', [CampaignMasterController::class, 'index'])->name('campaign-master.index');
    Route::match(['get', 'post'], 'campaign-master/add', [CampaignMasterController::class, 'add'])->name('campaign-master.add');
    Route::post('campaign-master/dataTableView', [CampaignMasterController::class, 'dataTableView'])->name('campaign-master.dataTableView');
    Route::match(['get', 'post'], 'campaign-master/edit/{encId}', [CampaignMasterController::class, 'edit'])->name('campaign-master.edit');

    // Campaign
    Route::get('/campaign', [CampaignController::class, 'index'])->name('campaign.index');
    Route::match(['get', 'post'], 'campaign/add', [CampaignController::class, 'add'])->name('campaign.add');
    Route::post('campaign/dataTableView', [CampaignController::class, 'dataTableView'])->name('campaign.dataTableView');
    Route::match(['get', 'post'], 'campaign/edit/{encId}', [CampaignController::class, 'edit'])->name('campaign.edit');


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

    // Bus info module
    Route::get('/businfo', [BusInfoController::class, 'businfo'])->name('businfo.index');
    Route::get('/businfo/preview', [BusInfoController::class, 'preview'])->name('businfo.preview');
    Route::match(['get', 'post'], 'businfo/add', [BusInfoController::class, 'add'])->name('businfo.add');
    Route::post('get-city-search', [BusInfoController::class, "getcity"]);

    //Review Catagory
    Route::get('/reviewcategory', [ReviewCategoryController::class, 'reviewCategory'])->name('reviewcategory.index');
    Route::match(['get', 'post'], 'reviewcategory/add', [ReviewCategoryController::class, 'add'])->name('reviewcategory.add');
    Route::post('reviewcategory/dataTableView', [ReviewCategoryController::class, 'dataTableView'])->name('reviewcategory.dataTableView');
    Route::match(['get', 'post'], 'reviewcategory/edit/{encId}', [ReviewCategoryController::class, 'edit'])->name('reviewcategory.edit');

    // Seat Layout
    Route::get('/seat-layout', [SeatLayoutController::class, 'index'])->name('seatlayout.index');
    Route::match(['get', 'post'], 'seat-layout/add', [SeatLayoutController::class, 'add'])->name('seatlayout.add');
    Route::post('seat-layout/dataTableView', [SeatLayoutController::class, 'dataTableView'])->name('seat-layout.dataTableView');
    Route::post('/check-layout-name', [SeatLayoutController::class, 'checkLayoutName']);
    Route::match(['get', 'post'], 'seat-layout/edit/{encId}', [SeatLayoutController::class, 'edit'])->name('seat-layout.edit');

    //View Bus Seat Layout
    Route::get('/bus-seat-layout/{id}', [BusSeatLayoutController::class, "index"])->name('buslayout.index');
    Route::post('/get-seat-layout-preview', [SeatLayoutController::class, "getSeatLyoutPreview"]);

    //--------------------------------------------------------------------------------------------------------------------


    //Add By Chakra
    Route::get('/blog-category', [BlogCategoryController::class, 'index'])->name('blog-category.index');
    Route::match(['get', 'post'], 'blog-category/add', [BlogCategoryController::class, 'add'])->name('blog-category.add');
    Route::post('blog-category/dataTableView', [BlogCategoryController::class, 'dataTableView'])->name('blog-category.dataTableView');
    Route::match(['get', 'post'], 'blog-category/edit/{encId}', [BlogCategoryController::class, 'edit'])->name('blog-category.edit');

    Route::get('/bus-schedule', [BusScheduleController::class, 'index'])->name('bus-schedule.index');
    Route::match(['get', 'post'], 'bus-schedule/add', [BusScheduleController::class, 'add'])->name('bus-schedule.add');
    Route::post('bus-schedule/dataTableView', [BusScheduleController::class, 'dataTableView'])->name('bus-schedule.dataTableView');
    Route::match(['get', 'post'], 'bus-schedule/edit/{encId}', [BusScheduleController::class, 'edit'])->name('bus-schedule.edit');

    Route::get('/bus-cancel', [BusCancelController::class, 'index'])->name('bus-cancel.index');
    Route::match(['get', 'post'], 'bus-cancel/add', [BusCancelController::class, 'add'])->name('bus-cancel.add');
    Route::post('bus-cancel/dataTableView', [BusCancelController::class, 'dataTableView'])->name('bus-cancel.dataTableView');
    Route::match(['get', 'post'], 'bus-cancel/edit/{encId}', [BusCancelController::class, 'edit'])->name('bus-cancel.edit');
    Route::post('get-bus-schedule-by-month', [BusCancelController::class, 'getBusScheduleDatesByMonth']);
    Route::post('get-cancelled-bus-data', [BusCancelController::class, 'getCancelledBusData']);

    Route::get('/seat-block', [SeatBlockController::class, 'index'])->name('seat-block.index');
    Route::match(['get', 'post'], 'seat-block/add', [SeatBlockController::class, 'add'])->name('seat-block.add');
    Route::post('seat-block/dataTableView', [SeatBlockController::class, 'dataTableView'])->name('seat-block.dataTableView');
    Route::match(['get', 'post'], 'seat-block/edit/{encId}', [SeatBlockController::class, 'edit'])->name('seat-block.edit');

    Route::get('/seat-open', [SeatOpenController::class, 'index'])->name('seat-open.index');
    Route::match(['get', 'post'], 'seat-open/add', [SeatOpenController::class, 'add'])->name('seat-open.add');
    Route::post('seat-open/dataTableView', [SeatOpenController::class, 'dataTableView'])->name('seat-open.dataTableView');
    Route::match(['get', 'post'], 'seat-open/edit/{encId}', [SeatOpenController::class, 'edit'])->name('seat-open.edit');

    Route::get('/extra-seat-block', [ExtraSeatBlockController::class, 'index'])->name('extra-seat-block.index');
    Route::match(['get', 'post'], 'extra-seat-block/add', [ExtraSeatBlockController::class, 'add'])->name('extra-seat-block.add');
    Route::post('extra-seat-block/dataTableView', [ExtraSeatBlockController::class, 'dataTableView'])->name('extra-seat-block.dataTableView');
    Route::match(['get', 'post'], 'extra-seat-block/edit/{encId}', [ExtraSeatBlockController::class, 'edit'])->name('extra-seat-block.edit');
});

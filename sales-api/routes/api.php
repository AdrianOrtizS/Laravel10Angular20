<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\NotificationController;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategorieController;

use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\Tarifa_iceController;

use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\ReceivableController;
use App\Http\Controllers\API\FacturaController;

use App\Http\Controllers\API\PointsOfSaleController;

use App\Http\Controllers\API\BuyController;
use App\Http\Controllers\API\PayController;

use App\Http\Controllers\API\ConfigurationController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\ReportsController;

use Barryvdh\DomPDF\Facade\Pdf;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register',     [AuthController::class, 'register']);
Route::get("getBranches",   [AuthController::class, 'getBranches']);

Route::get("getPointsByBranch/{id_branch}", [PointsOfSaleController::class, 'getPointsByBranch']);

Route::post('login',    [AuthController::class, 'login']);
Route::post('logout',   [AuthController::class, 'logout']);
Route::post('verified_auth', [AuthController::class, 'verified_auth']);

//  recuperar contraseña
Route::post('recover_password_email',   [AuthController::class, 'recover_password_email']);
Route::post('update_password_for_code',          [AuthController::class, 'update_password_for_code']);


Route::middleware('auth:api')->group(function () {
    
    Route::post('refresh',  [AuthController::class, 'refresh']);
    Route::get('me',        [AuthController::class, 'me']);
    Route::put('update',    [AuthController::class, 'update']);
    Route::post('updateUserLog',    [AuthController::class, 'updateUserLog']);
    
    Route::put('update_password_userLog', [AuthController::class, 'update_password_userLog']);

    Route::put('change_password', [AuthController::class, 'change_password']);
    
    Route::post('upload',       [FileController::class, 'upload']);
    Route::post('send-whatsapp',[NotificationController::class, 'sendWhatsApp']);
    Route::post('send-email',   [NotificationController::class, 'sendEmail']);
    
    Route::resource("categories", CategorieController::class);
    Route::post("categories/{id}", [CategorieController::class, 'update']);
    
    Route::resource("products", ProductController::class);
    Route::post("products/{id}",    [ProductController::class, 'update']);
    Route::get("getCategories",     [ProductController::class, 'getCategories']);
    Route::get("getTarifasIva",     [ProductController::class, 'getTarifasIva']);
    Route::get("getTarifasIce",     [ProductController::class, 'getTarifasIce']);

    Route::resource("branches", BranchController::class);
    

    Route::resource("users", UserController::class);
    Route::post("users/{id}", [UserController::class, 'update']);
    
    Route::get("getBranches", [UserController::class, 'getBranches']);
    Route::get("getPointsOfSale", [UserController::class, 'getPointsOfSale']);


    Route::resource("customers", CustomerController::class);
    Route::resource("suppliers", SupplierController::class);

    Route::resource("ice_tarifas", Tarifa_iceController::class);

    Route::resource('configurations', ConfigurationController::class);
    Route::post("configurations/{id}", [ConfigurationController::class, 'update']);


    Route::resource("sales", SaleController::class);
    Route::get("sale/getCustomers", [SaleController::class, 'getCustomers']);
    Route::get("sale/getProducts",  [SaleController::class, 'getProducts']);
    Route::get('sale/getByStatus',  [SaleController::class, 'getByStatus']);
    
    Route::get('sale/factura/{id}/pdf', [SaleController::class, 'pdf']);
    


    Route::get('sale/factura/{clave}/rePrintFacturaPdf', [SaleController::class, 'rePrintFacturaPdf']);
    Route::get('sale/factura/reconsultar/{id}', [SaleController::class, 'reconsultarSri']);
    Route::get('sale/generate_barcode/{clave_acceso}', [SaleController::class, 'barcodeGeneratorPng']);
    Route::post('sale/sendFacturaPdfXml/{clave}/{mailCustomerSale}', [SaleController::class, 'sendFacturaPdfXml']);

    Route::resource("receivables", ReceivableController::class);
    Route::resource("pointsOfSale", PointsOfSaleController::class);

    Route::get('sale/receivable/{id}/pdf', [ReceivableController::class, 'pdf']);
    Route::get('sale/receivable/{id}/rePrintPdf', [ReceivableController::class, 'rePrintPdf']);

    Route::prefix('reports')->group(function () {
        Route::get('/sales/monthly',    [ReportsController::class, 'salesMonthly']);
        Route::get('/sales/daily',      [ReportsController::class, 'sales10Daily']);
        Route::get('/products/top',     [ReportsController::class, 'top10Products']);
        Route::get('/inventory/low_stock', [ReportsController::class, 'lowStock']);

        Route::get('/purchases/monthly', [ReportsController::class, 'purchasesMonthly']);
    });

    Route::resource("buys", BuyController::class);
    Route::get("buy/getSuppliers", [BuyController::class, 'getSuppliers']);
    Route::get("buy/getProducts", [BuyController::class, 'getProducts']);
    Route::get('buy/getByStatus', [BuyController::class, 'getByStatus']);

    Route::resource("pays", PayController::class);
 
});


// routes/api.php
Route::get('/facturas/{claveAcceso}/estado', [SaleController::class, 'estadoSri']);

Route::post('factura/generar', [FacturaController::class, 'generar']);


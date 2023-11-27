<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\OrdersController;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\superadmin\SuperadminController;
use App\Http\Controllers\ForgotPasswordController;




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
Route::get('UpdatePricingWeight',[SuperadminController::class,'UpdatePricingWeight']);


Route::get('demotest',[ProductController::class,'demoTestProduct']);
Route::get('sync-products',[ProductController::class,'shopifyProductTest']);
Route::get('sync/{cid}',[ProductController::class,'shopifyProductSync']);
Route::get('createcollection',[TestController::class,'createCollection']);
Route::get('linkcollection',[TestController::class,'linkProductCollection']);
Route::get('pricecheck',[TestController::class,'checkpricelist']);
Route::get('pricelist',[TestController::class,'pricelist']);
Route::get('price',[TestController::class,'price']);
Route::get('conprice',[TestController::class,'conprice']);
Route::get('tcrotest',[TestController::class,'test']);

Route::get('testProduct',[TestController::class,'testProduct']);

Route::get('testprice',[TestController::class,'updateTestPrice']);
Route::get('testopt',[TestController::class,'testotp']);

Route::post('createOrderWebhook',[OrdersController::class,'createOrderWebhook']);
Route::get('yellowverandah',[ProductController::class,'fetchProductFromUrl']);

Route::get('pricecalculate',[TestController::class,'pricecalculate']);
Route::get('checkvariantimage',[TestController::class,'uploadeVariantImage']);
Route::group(['middleware'=>'guest'],function(){
    Route::get('/',[AdminController::class,'loginview'])->name('login');
   //Route::get('admin/login',[AdminController::class,'loginview'])->name('login');
   Route::post('login',[AdminController::class,'login'])->name('post.login');
   Route::get('admin/register',[AdminController::class,'registerview'])->name('admin.register');
   Route::post('register',[AdminController::class,'register'])->name('post.register');
});
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');


Route::group(['middleware'=>'auth'],function(){
Route::get('logout',[AdminController::class,'logout'])->name('admin.logout');
Route::get('home',[AdminController::class,'dashboard'])->name('home');
/////SuperAdmin
Route::group(['middleware'=>'superAdmin', 'prefix' => 'superadmin'],function(){
      Route::get('dashboard',[SuperadminController::class,'dashboard'])->name('superadmin.home');
      Route::post('change-vendor-status',[SuperadminController::class,'changeVendorStatus'])->name('superadmin.change-vendor-status');
	  Route::post('change-premium-status',[SuperadminController::class,'changePremiumStatus'])->name('superadmin.change-premium-status');
      Route::post('change-vendor-discount',[SuperadminController::class,'changeVendorDiscount'])->name('superadmin.change-vendor-discount');
      Route::get('products',[SuperadminController::class,'productlist'])->name('superadmin.allproduct');
	  Route::get('reject-products',[SuperadminController::class,'rejectProductList'])->name('superadmin.rejectProductList');
	  Route::get('approved-products/{day}',[SuperadminController::class,'approvedProductlist'])->name('superadmin.approvedproduct');
      Route::get('shopify-create/{id}',[SuperadminController::class,'createProductShopify']);
	  Route::get('reject-product/{id}',[SuperadminController::class,'rejectProduct']);
      Route::get('out-of-stock',[SuperadminController::class,'outofstockProduct'])->name('superadmin.out-of-stock');
	  Route::get('out-of-stock-products/{id}',[SuperadminController::class,'outofstockProductLists'])->name('superadmin.out-of-stock-products');
      Route::post('bulk-approve-product',[SuperadminController::class,'bulkApproveProduct'])->name('superadmin.bulk-approve-product');
	  Route::post('bulk-reject-product',[SuperadminController::class,'bulkRejectProduct'])->name('superadmin.bulk-reject-product');
      Route::get('store-configuration',[SuperadminController::class,'vendorlist'])->name('superadmin.store-configuration');
      Route::get('products-details/{id}',[SuperadminController::class,'productDetails'])->name('superadmin.productdetails');
    Route::get('orders',[SuperadminController::class,'orderlist'])->name('superadmin.allorders');


      //zain
    Route::get('settings',[SuperadminController::class,'Settings'])->name('superadmin.settings');
    Route::post('save-settings',[SuperadminController::class,'SaveSettings'])->name('superadmin.save_settings');


    Route::get('logs',[SuperadminController::class,'Logs'])->name('superadmin.logs');


    //15-06-2023
      Route::post('variant-details',[SuperadminController::class,'variantDetailUpdate'])->name('superadmin.variantdetails');
      Route::get('banner',[SuperadminController::class,'bannerlist'])->name('superadmin.banner');
      Route::post('change-banner-status',[SuperadminController::class,'changeBannerStatus'])->name('superadmin.change-banner-status');
	  Route::get('stores-products',[SuperadminController::class,'storesList']);
	  Route::get('store-products-csv/{id}',[SuperadminController::class,'pricecalculate']);

	  Route::get('/download/{file}',[SuperadminController::class,'download'])->name('download');
	  Route::get('/download_logo/{file}',[SuperadminController::class,'download_logo'])->name('download_logo');
	  Route::get('updateprice',[SuperadminController::class,'updateAllProductPrices']);
    Route::get('updatepricebyvendor/{id}',[SuperadminController::class,'updateProductPricesByVendor'])->name('update-price-by-vendor');
    Route::get('updatepricebyvendorinshopify/{id}',[SuperadminController::class,'updateProductPricesInShopify'])->name('update-price-by-vendor-inshopify');


    Route::get('updatepriceby-producttype',[SuperadminController::class,'updateProductPricesByProductType'])->name('superadmin.update.database-price-by-producttype');
    Route::get('update-shopify-priceby-producttype',[SuperadminController::class,'updateShopifyPricesByProductType'])->name('superadmin.update.shopify-price-by-producttype');



    Route::get('store-amount',[SuperadminController::class,'storeAmount'])->name('superadmin.store-amount');
	  Route::get('store-amount-history/{id}',[SuperadminController::class,'storeOrdersAmount']);
	  Route::get('order-details/{id}',[SuperadminController::class,'storeOrdersDetails'])->name('superadmin.orderdetail');
      Route::get('conversion-rate',[SuperadminController::class,'conversionRate']);
	  Route::post('update-conversion-rate',[SuperadminController::class,'updateConversionRate']);
	  Route::get('shipingchagres/{id}',[SuperadminController::class,'shipingCharges']);
	  Route::post('update-shiping-charges',[SuperadminController::class,'updateShipingCharges']);

      Route::get('vendor-list',[SuperadminController::class,'vendorlist'])->name('vendor-list');
      Route::get('vendor-details/{id}',[SuperadminController::class,'vendordetails'])->name('vendordetails');
      Route::post('vendor-general-configuration/{store}',[SuperadminController::class,'updateGeneralConfiguration'])->name('genralconfiguration.update');
      Route::post('vendor-payment-details/{payment}',[SuperadminController::class,'updatePaymentDetails'])->name('paymentDetails.update');
      Route::post('vendor-store-front/{store}',[SuperadminController::class,'updateStoreFront'])->name('storeFront.update');
      Route::post('update-vendor-tag/{id}',[SuperadminController::class,'updateVendorTag'])->name('update.vendor.tag');
      Route::get('documents',[SuperadminController::class,'documentslist'])->name('documents');
      Route::get('/downloadfile/{file}',[SuperadminController::class,'downloadfile'])->name('downloadfile');
      Route::post('submitdocument',[SuperadminController::class,'submitdocument'])->name('submitdocument');
      Route::get('profile',[SuperadminController::class,'profile'])->name('profile');
      Route::post('saveprofile',[SuperadminController::class,'saveuserprofile'])->name('save-profile');
      Route::get('remove-profile{id}',[SuperadminController::class,'removeprofilepicture'])->name('remove-profile');
      Route::post('changepassword',[SuperadminController::class,'password'])->name('change-password');
      Route::get('/change',[SuperadminController::class,'changestatus'])->name('change-status');
      Route::get('banner-list',[SuperadminController::class,'bannerlist'])->name('banner-list');

	  Route::get('fetch-product-form',function(){ return view('superadmin.fetch-product-url');});
	  Route::post('fetch-product-url',[SuperadminController::class,'fetchProductUrl'])->name('fetch-product-url');

	  Route::get('uploade-product-form',function(){ return view('superadmin.uploade-product-form');});
	  Route::post('uploade-bulk-products',[SuperadminController::class,'uploadeBulkProducts']);


    Route::get('vendors',[SuperadminController::class,'vendors'])->name('vendors');
    Route::get('vendor-setting/{id}',[SuperadminController::class,'vendorSetting'])->name('vendor.setting');
    Route::post('update-hsn-code',[SuperadminController::class,'updatehsncode'])->name('superadmin.update.hsncode');
    Route::post('update-record',[SuperadminController::class,'updaterecord'])->name('superadmin.update.record');


    Route::post('vendor-setting',[SuperadminController::class,'Vendorbaseweightupdate'])->name('superadmin.baseweight.update');
    Route::post('update-market-bulkprice',[SuperadminController::class,'updatemarketbulkprice'])->name('superadmin.update.market-bulkprice');
    Route::post('update-product-detail',[SuperadminController::class,'updateproductdetail'])->name('superadmin.updateproductdetails');

    Route::post('update-product-type-sizechart',[SuperadminController::class,'updateProductTypeSizechart'])->name('superadmin.update-product-type-sizechart');

    Route::get('delete-producttype-img',[SuperadminController::class,'deleteProductTypeImage'])->name('superadmin.delete.product-type-img');
    Route::get('delete-setting-img',[SuperadminController::class,'deleteSettingImage'])->name('superadmin.delete.setting-img');


    Route::get('approve-selected-products',[SuperadminController::class,'approveSelectedProducts'])->name('superadmin.approve-selected-products');
    Route::get('deny-selected-products',[SuperadminController::class,'denySelectedProducts'])->name('superadmin.deny-selected-products');



    Route::get('add-product-type-sizechart/{id}',[SuperadminController::class,'addProductTypeSizeChart'])->name('superadmin.add-product-type-sizechart');
    Route::post('save-product-type-subcategory',[SuperadminController::class,'saveProductTypeSubCategory'])->name('superadmin.save-product-type-subcategory');
    Route::get('delete-product-type-subcategory-img',[SuperadminController::class,'deleteProductTypeSubCategoryImage'])->name('superadmin.delete.product-type-subcategory-img');
    Route::post('update-product-type-subcategory',[SuperadminController::class,'updateProductTypeSubCategory'])->name('superadmin.update-product-type-subcategory');
    Route::get('delete-product-type-subcategory/{id}',[SuperadminController::class,'deleteProductTypeSubCategory'])->name('superadmin.delete-product-type-subcategory');
    Route::get('update-product-shopify-status',[SuperadminController::class,'UpdateProductShopifyStatus'])->name('superadmin.update.product.shopifystatus');



    Route::get('start-shopifypush-cronjob/{id}',[SuperadminController::class,'startShopifyPushCronjob'])->name('start.shopifypush.cronjob');
    Route::get('pause-shopifypush-cronjob/{id}',[SuperadminController::class,'pauseShopifyPushCronjob'])->name('pause.shopifypush.cronjob');




});
Route::group(['middleware'=>'products'],function(){
     Route::get('product-list',[ProductController::class,'productlist'])->name('product-list');
     Route::get('add-product',[ProductController::class,'productview'])->name('add-product');
     Route::post('save-product',[ProductController::class,'saveproduct'])->name('save-product');
     //Route::get('product-list',[ProductController::class,'productlist'])->name('product-list');
     Route::get('delete-product/{id}',[ProductController::class,'deleteproduct'])->name('delete-product');
     Route::get('edit-product/{id}',[ProductController::class,'editproduct'])->name('edit-product');
     Route::post('save-products',[ProductController::class,'saveproducts'])->name('save-products');
     Route::get('edit-variant/{id}',[ProductController::class,'editVariant'])->name('edit-variant');
     Route::post('update-variant',[ProductController::class,'updateVariant']);
     Route::post('delete-variant',[ProductController::class,'deleteVariant'])->name('delete-variant');
     Route::get('add-new-variant/{id}',[ProductController::class,'addNewVariant']);
     Route::post('save-new-variant',[ProductController::class,'saveNewVariant']);

	 Route::get('category',[ProductController::class,'allCategory']);
	 Route::get('add-category',[ProductController::class,'addCategory']);
	 Route::post('save-category',[ProductController::class,'saveCategory'])->name('save-category');
	 Route::get('edit-category/{id}',[ProductController::class,'editCategory']);
	 Route::post('update-category',[ProductController::class,'updateCategory']);
	 Route::get('delete-category/{id}',[ProductController::class,'deleteCategory']);
});
Route::group(['middleware'=>'marketing'],function(){
    Route::get('manage-discount',[DiscountController::class,'discountlist'])->name('manage-discount');
    Route::get('add-discount',[DiscountController::class,'addDiscount']);
    Route::post('save-discount',[DiscountController::class,'saveDiscount']);
    Route::get('discount-delete/{id}',[DiscountController::class,'deleteDiscount']);
    Route::get('discount-edit/{id}',[DiscountController::class,'editDiscount']);
    Route::post('savebanner',[BannerController::class,'savebanner']);

	Route::get('manage-product-discount',[DiscountController::class,'productsDiscountlist'])->name('manage-product-discount');
	Route::get('product-add-discount',[DiscountController::class,'productsDiscountAddForm']);
	Route::post('save-product-discount',[DiscountController::class,'saveProductDiscount']);
	Route::get('delete-product-discount',[DiscountController::class,'deleteProductsDiscount']);
	Route::get('delete-store-product-discount',[DiscountController::class,'deleteStoreProductsDiscount']);
});
Route::group(['middleware'=>'storeconfig'],function(){
    Route::get('admin/general-config',[AdminController::class,'generalconfig'])->name('admin.generalconfig');
    Route::post('admin/submit-general-config',[AdminController::class,'submitgeneralconfig'])->name('post.generalconfig');
     Route::get('admin/store-front',[AdminController::class,'storefront'])->name('admin.storefront');
     Route::post('admin/submit-store-front',[AdminController::class,'submitstorefront'])->name('post.storefront');
     Route::get('admin/payment-configuration',[PaymentController::class,'Paymentconfig'])->name('admin.paymentconfig');
     Route::get('admin/edit-payment-configuration',[PaymentController::class,'editPaymentconfig'])->name('admin.editpaymentconfig');
     Route::post('admin/submit-payment-configuration',[PaymentController::class,'submitPaymentconfig'])->name('post.editpaymentconfig');
});
Route::group(['middleware'=>'orders'],function(){
     ////////////Orders
     Route::get('allorders',[OrdersController::class,'fetchShopifyOrders']);
     Route::get('orders',[OrdersController::class,'allOrders']);
     Route::get('open-orders',[ProductController::class,'openOrders']);
     Route::get('pickup-orders',[OrdersController::class,'pickupOrders'])->name('pickup-orders');
     Route::get('complete-orders',[OrdersController::class,'completeOrders'])->name('complete-orders');
     Route::get('order-details/{id}',[OrdersController::class,'detailsOrders']);
     Route::get('new-orders', [OrdersController::class,'newOrders']);
     Route::get('change-status/{oid}/{status}',[OrdersController::class,'changeOrderStatus']);
});
 Route::get('admin/profile',[AdminController::class,'editprofile'])->name('admin.editprofile');
 Route::post('admin/save-profile',[AdminController::class,'saveprofile'])->name('admin.saveprofile');
 Route::post('admin/change-password',[AdminController::class,'changepassword'])->name('admin.changepassword');
 Route::get('admin/banner',[BannerController::class,'bannerview'])->name('admin.banner');
 Route::post('home-desktop-banner',[BannerController::class,'submithomedesktopbanner'])->name('home-desktop-banner');
 Route::get('admin/document',[DocumentController::class,'documentview'])->name('admin.document');
 Route::post('admin/save-document',[DocumentController::class,'savedocument'])->name('post.document');
 Route::get('/downloaddocument/{file}',[DocumentController::class,'downloaddocument'])->name('downloaddocument');
 Route::post('home-mobile-banner',[BannerController::class,'submithomemobilebanner'])->name('home-mobile-banner');
 Route::post('store-desktop-banner',[BannerController::class,'submitstoredesktopbanner'])->name('store-desktop-banner');
 Route::post('store-mobile-banner',[BannerController::class,'submitstoremobilebanner'])->name('store-mobile-banner');
 Route::post('profile-image',[AdminController::class,'profileimage'])->name('profile-image');


  Route::get('user-role',[RoleController::class,'userrole'])->name('user-role')->middleware('vendoruser');
  Route::get('user-role-create',[RoleController::class,'roleCreate'])->name('role.create')->middleware('vendoruser');
  Route::get('role-edit/{id}',[RoleController::class,'edit']);
  Route::post('save-role',[RoleController::class,'saverole'])->name('save-role')->middleware('vendoruser');
  Route::post('update-role',[RoleController::class,'updaterole'])->name('update-role')->middleware('vendoruser');
  Route::resource('users', UserController::class)->middleware('vendoruser');
  Route::post('update-user/{id}',[UserController::class,'update'])->name('update-user')->middleware('vendoruser');
  Route::get('users-edit/{id}',[UserController::class,'edit']);
  Route::get('users-delete/{id}',[UserController::class,'destroy']);


 ////Prdeep route
 Route::post('update-products',[ProductController::class,'updateProducts']);
 Route::get('out-of-stock',[ProductController::class,'outOfStockProductsList']);
 Route::get('delete-image/{id}',[ProductController::class,'deleteImage']);
 Route::get('update-stock',[ProductController::class,'updateStock']);
 Route::get('shopify-create-cron',[ProductController::class,'createProductShopifyMultiple']);
 Route::get('shopify-create/{id}',[ProductController::class,'createProductShopify']);
 Route::get('shopify-update/{id}',[ProductController::class,'updateProductShopify']);
 Route::get('shopify-orders',[ProductController::class,'fetchShopifyOrders']);



 ////Discount part



 Route::get('testcron',[ProductController::class,'cronInventoryUpdate']);
 //Route::get('fetch_products/{url}',[ProductController::class,'fetchProductFromUrl']);

 Route::get('testevent',[TestController::class,'testEvent']);



Route::get('testinv',[ProductController::class,'testinv']);
Route::get('testcode',[ProductController::class,'testcode']);

 Route::get('testimg',[ProductController::class,'uploadeImage']);
 Route::get('curltest',[ProductController::class,'curlTest']);
 Route::get('exportProductView',[ProductController::class,'exportProductView']);
 Route::get('exportProduct',[ProductController::class,'exportProduct']);
 Route::post('import',[ProductController::class,'importProduct']);
 Route::post('import-inventory',[ProductController::class,'importInventory']);
 Route::get('importProduct',[ProductController::class,'importProductView']);
});

 //user route...
   Route::get('user/login',[UserLoginController::class,'userloginview'])->name('user-login');
   Route::post('login/user',[UserLoginController::class,'userlogin'])->name('post.userlogin');
   Route::get('user/logout',[UserLoginController::class,'userlogout'])->name('post.userlogout');











<?php

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
Route::get('/', 'WelcomeController@index')->name('welcome');

Auth::routes();
Route::get('/redirect/{social}', 'SocialAuthController@redirect');
Route::get('/callback/{social}', 'SocialAuthController@callback');
Route::get('category/NewArrival', 'ProductController@getNewArrival');
Route::get('category/TopSell', 'ProductController@getTopSell');
Route::get('category/{group}/{name}', 'ProductController@getCategoryProduct');
Route::get('test/{id}', 'CategoryController@test');

Route::post('upload', 'UploadController@upload');

// Route to user profile
Route::get('profile', [
    'as' => 'profile',
    'uses' => 'ProfileController@getProfile',
]);
Route::post('sua_profile', [
    'as' => 'sua_profile',
    'uses' => 'ProfileController@postProfile',
]);
Route::get('edit_profile', [
    'as' => 'edit-profile-user',
    'uses' => 'ProfileController@getEditProfile',
]);

//product_details
Route::get('/product_details/{product_id}', 'ProductController@getProductDetails')->name('product_details')->middleware('product_status');
//shop_details
Route::get('/shop_details/{shop_product_id}', 'ShopDetailsController@getDetails')->name('shop_details');



//search
Route::post('search', 'ProductController@search')->name('search');
Route::get('search/{name}', 'ProductController@searchName')->name('search_name');

Route::post('/add_cart/{produc_id}', 'AddCartController@create')->name('add_cart');

Route::get('view_cart/{user_id}', 'ViewCartController@viewCart')->name('view_cart')->middleware('auth');
// Route to admin
Route::group(['prefix' => 'admin'], function(){
    Route::get('/', 'AdminController@getView')->middleware(['admin']);
    Route::group(['prefix' => 'category'],function(){
        //admin/category/cate_list
        Route::get('cate_list', 'AdminController@getList')->middleware(['admin']);
        Route::get('cate_list_child/{category_id}', 'AdminController@getListChild')->name('cate_list_child')->middleware(['admin']);
        Route::get('add', 'AdminController@getAdd')->middleware(['admin']);
        Route::post('add_cate', 'AdminController@postAdd')->name('add_cate')->middleware(['admin']);
        Route::get('cate_list/{id}/edit', 'AdminController@getEdit')->middleware(['admin']);
        Route::post('edit_category/{id}', 'AdminController@postEdit')->middleware(['admin']);
        Route::get('delete/{id}', 'AdminController@getDelete')->middleware(['admin']);
    });
    Route::group(['prefix' => 'product'],function(){
        //admin/product/product_list
        Route::get('product_list', 'AdminController@getProductList');
        Route::get('edit_product', 'AdminController@getEditProduct');
        Route::get('add_product', 'AdminController@getAddProduct');
        Route::post('add_product_post', 'AdminController@postAddProduct')->name('add_product_post');
        Route::get('product_list/{id}/edit_product', 'AdminController@getEditProduct');
        Route::post('edit_product_post/{id}', 'AdminController@postEditProduct');
        Route::get('delete/{id}', 'AdminController@getDeleteProduct');
        Route::get('category_child/{id}', 'AjaxController@getCategory');
    });
    Route::group(['prefix' => 'user'], function(){
        //admin/user/user_list
        Route::get('user_list', 'AdminController@getUserList');
        Route::get('admin_list', 'AdminController@getUserListAdmin');
        Route::get('customer_list', 'AdminController@getUserListCustomer');
        Route::get('setup_user/{id}', 'AdminController@getSetupUser');
        Route::get('delete/{id}', 'AdminController@getDeleteUser');
    });
    Route::group(['prefix' => 'order'], function(){
        //admin/order/order_list
        Route::get('order_list', 'AdminController@getOrderList');
        Route::get('order_today', 'AdminController@getOrderListToday');
        Route::get('order_doing', 'AdminController@getOrderListDoing');
        Route::get('order_done', 'AdminController@getOrderListDone');
        Route::get('orderdetail_list/{order_id}', 'AdminController@getDetailOrder')->name('orderdetail_list');
        Route::get('delete/{id}', 'AdminController@getDeleteOrder');
        Route::get('edit_order/{id}', 'AdminController@getEditOrder');
    });
    Route::group(['prefix' => 'ajax'], function(){

    });
});

Route::get('checkout_addresses/{order_id}', 'CheckOutController@get_checkout_addresses')->name('checkout_addresses')->middleware(['auth', 'cart']);

Route::post('checkout_addresses/{user_id}/{order_id}', 'CheckOutController@checkout_addresses' )->name('checkout_addresses_confirm');

Route::get('checkout_payment/{order_id}', function() {
    return view('checkout/checkout_payment');
})->name('checkout_payment');

Route::post('checkout_payment/{user_id}/{order_id}', 'CheckOutController@checkout_payment')->name('checkout_payment_confirm');

Route::get('checkout_confirm/{order_id}', 'CheckOutController@checkout_confirm')->name('checkout_confirm');

Route::post('checkout_confirm/{user_id}/{order_id}', 'CheckOutController@checkout_comfirm_done')->name('checkout_confirm_done');

//add product
Route::get('plus_product/{order_detail_id}', 'AddCartController@plus_product')->name('add_product');

Route::get('minus_product/{order_detail_id}', 'AddCartController@minus_product')->name('minus_product');
Route::get('delete_product/{order_detail_id}', 'AddCartController@delete_product')->name('delete_product');

//history
Route::get('view_cart/order/{id}', 'ViewCartController@viewCartWithId')->name('view_cart_id')->middleware(['auth','check_status_order']);

//show history orders
Route::get('history_orders', 'HistoryOrdersController@showHistory');

//comment
Route::resource('comment', 'CommentController');
Route::get('comment/{comment_id}/delete', [
        'as' => 'comment.delete',
        'uses' => 'CommentController@destroy',
    ]);
Route::post('/editComment', 'CommentController@updateComment');
Route::get('/replyComment', 'CommentController@replyComment');

//rate
Route::resource('rate', 'RateController', [
        'only' => ['store']
    ]);
//productShop
Route::get('product_shop/{shop_product_id}', 'ShopProductController@show_product')->name('product_shop');
//Contact
Route::get('contact', 'ContactController@getContact')->name('get_contact');
Route::post('contact', 'ContactController@postContact')->name('post_contact');


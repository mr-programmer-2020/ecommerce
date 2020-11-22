<?php

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


/*
|----------------------------------------------------------------------------------------------------
| this is Route::prefix('admin')  << it is in RouteServiceProvider so it is always with /admin prefix
|----------------------------------------------------------------------------------------------------
*/  

define('PAGINATION_COUNT',10); 

//only with login and also must be admin can enter this controller 
Route::group(['namespace' => 'Admin' ,'middleware' => 'auth:admin'],function(){ 
Route::get('/','DashBoardController@index') ->name('admin.dashboard');

######################## begin languages route ###############################
Route::group(['prefix' => 'languages'],function(){
Route::get('/','LanguagesController@index')->name('admin.languages');  
Route::get('create','LanguagesController@create')->name('admin.languages.create'); 
Route::post('store','LanguagesController@store')->name('admin.languages.store'); 

Route::get('edit/{id}','LanguagesController@edit')->name('admin.languages.edit'); 
Route::post('update/{id}','LanguagesController@update')->name('admin.languages.update'); 

Route::get('delete/{id}','LanguagesController@destroy')->name('admin.languages.delete'); 
});
######################## end languages route ################################


######################## begin Main Categories route ###############################
Route::group(['prefix' => 'main_cateories'],function(){ 
Route::get('/','MainCategoriesController@index')->name('admin.maincategories');  
Route::get('create','MainCategoriesController@create')->name('admin.maincategories.create'); 
Route::post('store','MainCategoriesController@store')->name('admin.maincategories.store'); 

Route::get('edit/{id}','MainCategoriesController@edit')->name('admin.maincategories.edit'); 
Route::post('update/{id}','MainCategoriesController@update')->name('admin.maincategories.update'); 

Route::get('delete/{id}','MainCategoriesController@destroy')->name('admin.maincategories.delete'); 
Route::get('changeStatus/{id}','MainCategoriesController@changeStatus')->name('admin.maincategories.status'); 
});
######################## end Main Categories route ################################


######################## begin SUb Categories route ###############################
Route::group(['prefix' => 'sub_cateories'],function(){ 
Route::get('/','SubCategoriesController@index')->name('admin.subcategories');  
Route::get('create','SubCategoriesController@create')->name('admin.subcategories.create'); 
Route::post('store','SubCategoriesController@store')->name('admin.subcategories.store'); 

Route::get('edit/{id}','SubCategoriesController@edit')->name('admin.subcategories.edit'); 
Route::post('update/{id}','SubCategoriesController@update')->name('admin.subcategories.update'); 

Route::get('delete/{id}','SubCategoriesController@destroy')->name('admin.subcategories.delete'); 
Route::get('changeStatus/{id}','SubCategoriesController@changeStatus')->name('admin.subcategories.status'); 
});
######################## end Sub Categories route ################################


######################## begin vendors route ###############################
Route::group(['prefix' => 'vendors'],function(){ 
Route::get('/','VendorsController@index')->name('admin.vendors');  
Route::get('create','VendorsController@create')->name('admin.vendors.create'); 
Route::post('store','VendorsController@store')->name('admin.vendors.store'); 

Route::get('edit/{id}','VendorsController@edit')->name('admin.vendors.edit'); 
Route::post('update/{id}','VendorsController@update')->name('admin.vendors.update'); 

Route::get('delete/{id}','VendorsController@destroy')->name('admin.vendors.delete');
Route::get('changeStatus/{id}','VendorsController@changeStatus')->name('admin.vendors.status');  
});
######################## end vendors route ################################

});


// guest:admin anyone will be able to visit it the admin and not the admin also 
Route::group(['namespace' => 'Admin' ,'middleware' => 'guest:admin'],function(){ 
Route::get('login','LoginController@getLogin')->name('get.admin.login');
Route::post('login','LoginController@login')->name('admin.login'); 
}); 
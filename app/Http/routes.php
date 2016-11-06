<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('dummyEntry', function () {
    return view('dummyEntry');
});

Route::get('test', function () {
//    $path = storage_path('logs/laravel-2016-11-05.log');
//    $path = "/Users/chaolu/Desktop/Projects/File_Storage/Procurement/Projects/1/laravel-2016-11-05.log";
    $path = "/Users/chaolu/Desktop/Projects/File_Storage/Procurement/Projects/1/eAppointment.pdf";

    return response()->file($path);
})->name('test');

Route::group(['middleware' => ['normal']], function () {
    Route::get('role/list', 'RoleController@listPage')->name(ROUTE_NAME_ROLE_LIST);
    Route::post('role/remove', 'RoleController@remove')->name(ROUTE_NAME_ROLE_REMOVE);
    Route::post('role/add', 'RoleController@add')->name(ROUTE_NAME_ROLE_ADD);
    Route::post('role/select', 'RoleController@select')->name(ROUTE_NAME_ROLE_SELECT);

    Route::get('project/apply', 'ProjectController@apply')->name(ROUTE_NAME_PROJECT_APPLY);
    Route::post('project/create', 'ProjectController@create')->name(ROUTE_NAME_PROJECT_CREATE);
    Route::get('project/display/{id}', 'ProjectController@display')->name(ROUTE_NAME_PROJECT_DISPLAY);
    Route::get('project/list', 'ProjectController@listPage')->name(ROUTE_NAME_PROJECT_LIST);

    Route::get('document/display/{id}', 'DocumentController@display')->name(ROUTE_NAME_DOCUMENT_DISPLAY);
});

Route::group(['middleware' => ['welcome']], function () {

    Route::match(['get', 'post'], 'welcome', 'WelcomeController@index')->name(ROUTE_NAME_WELCOME);
});


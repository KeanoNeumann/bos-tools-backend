<?php

use App\Http\Controllers\InvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/invitations', [InvitationController::class, 'store'])->middleware('auth');

Route::middleware('auth:sanctum')->namespace("App\Http\Controllers")->group(function () {
    Route::get('/tenant/{tenant}', 'TenantController@get');
});


Route::middleware('auth:sanctum')->group(function () {
    // Groups
    Route::namespace("App\Http\Controllers")->group(function () {
        Route::get('/group/all', 'GroupController@allAdmin');
        Route::put('/group', 'GroupController@create');
        Route::post('/group/{group}', 'GroupController@update');
        Route::delete('/group/{group}', 'GroupController@delete');
    });
});


Route::group([
    'prefix' => '/{tenant}',
    'middleware' => [InitializeTenancyByPath::class],
], function () {

    Route::middleware('auth:sanctum')->group(function () {
        // Organisations
        Route::namespace("App\Http\Controllers")->group(function () {
            Route::get('/organisation/all/admin', 'OrganisationController@allAdmin');
            Route::put('/organisation', 'OrganisationController@create');
            Route::post('/organisation/{organisation}/update', 'OrganisationController@update');
            Route::delete('/organisation/{organisation}', 'OrganisationController@delete');

            Route::get('/organisation/{organisation}/groups', 'OrganisationController@groups');
        });

        // User
        Route::namespace("App\Http\Controllers")->group(function () {
            Route::get('/user/all', 'UserController@all');
            Route::get('/user/{organisation}/all', 'UserController@allForOrganisation');
            Route::post('/user/{user}/create', 'UserController@create');
            Route::post('/user/{user}/update', 'UserController@update');
            Route::post('/user/{user}/delete', 'UserController@delete');
        });
    });
});

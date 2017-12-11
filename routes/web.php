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

Route::get('/', function () {
    return view('welcome');
});

Route::post('auth/login',		 	['as' => 'login', 			'uses' => 'Auth\LoginController@login']);
Route::get('auth/logout',		 	['as' => 'logout', 			'uses' => 'Auth\LoginController@logout']);
// Registration routes...
Route::post('auth/register',		['as' => 'register', 		'uses' => 'Auth\RegisterController@register']);
// Password reset link request routes...
Route::post('password/email',		['as' => 'email_password', 	'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::post('password/reset',		['as' => 'reset_password', 	'uses' => 'Auth\ResetPasswordController@reset']);
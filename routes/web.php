<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactFormSubmissionController;
use Spatie\Honeypot\ProtectAgainstSpam;

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

Route::get('/', 'App\Http\Controllers\HomepageController@index');
Route::get('/json', 'App\Http\Controllers\HomepageController@json')->name('vehicles-json');
Route::get('/vehicle-detail/{id}', 'App\Http\Controllers\HomepageController@vehicleDetail')->name('vehicles-detail');
Route::get('/vehicle-update/{id}', 'App\Http\Controllers\HomepageController@vehicleUpdate')->name('vehicles-update');
Route::post('/save', 'App\Http\Controllers\HomepageController@save')->name('vehicle-form')->middleware(ProtectAgainstSpam::class);
Route::post('/save-update', 'App\Http\Controllers\HomepageController@saveUpdate')->name('vehicle-form-save')->middleware(ProtectAgainstSpam::class);

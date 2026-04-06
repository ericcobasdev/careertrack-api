<?php

use Illuminate\Support\Facades\Route;

/**
 * Defines a GET route for the root URL '/'.
 * 
 * This route returns the 'welcome' view, typically used as the homepage
 * or landing page of the application.
 
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', function () {
    return response()->json([
        'name' => 'CareerTrack API',
        'version' => '1.0',
        'status' => 'OK'
    ]);
});
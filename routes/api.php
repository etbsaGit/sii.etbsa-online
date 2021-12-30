<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('mailable', function (Request $request) {
    $data = $request;
    $email = 'tienda@etbsa.com.mx';
    $pdf = \PDF::loadView('pdfs.funding.details', compact('data'));
    Mail::to($data->email)->cc($email)->send(new FundingRequest($pdf));
    return response()->json($data);
});
Route::get('test', function (Request $request) {
    return response()->json(['hello']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });
Route::post('/list', 'Axistrustee\Covenants\Http\Controllers\CovenantController@list');
Route::post('/update', 'Axistrustee\Covenants\Http\Controllers\CovenantController@update');
Route::post('/addClone', 'Axistrustee\Covenants\Http\Controllers\CovenantController@addClone');
Route::post('/view', 'Axistrustee\Covenants\Http\Controllers\CovenantController@view');
Route::post('/saveTimeline', 'Axistrustee\Covenants\Http\Controllers\CovenantController@saveTimeline');
Route::post('/resolution', 'Axistrustee\Covenants\Http\Controllers\CovenantController@resolution');
Route::post('/approve', 'Axistrustee\Covenants\Http\Controllers\CovenantController@approve');
Route::post('/approve-active', 'Axistrustee\Covenants\Http\Controllers\CovenantController@approveActive');
Route::post('/submitForApproval', 'Axistrustee\Covenants\Http\Controllers\CovenantController@submitForApproval');
Route::post('/submitForApprovalActive', 'Axistrustee\Covenants\Http\Controllers\CovenantController@submitForApprovalActive');
Route::post('/pending-approval-list', 'Axistrustee\Covenants\Http\Controllers\CovenantController@pendingApproval');
Route::get('/approved-covenants', 'Axistrustee\Covenants\Http\Controllers\CovenantController@approvedCovenant');
Route::post('/active-list', 'Axistrustee\Covenants\Http\Controllers\CovenantController@activeList');
Route::post('/active-list-pending', 'Axistrustee\Covenants\Http\Controllers\CovenantController@activeListPending');
Route::post('/active-list-approved', 'Axistrustee\Covenants\Http\Controllers\CovenantController@activeListApproved');
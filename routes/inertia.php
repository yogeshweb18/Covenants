<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/', function (NovaRequest $request) {
    return inertia('Covenants');
});
/*Route::get('/pending-approval', function (NovaRequest $request) {
    return inertia('PendingApproval');
});*/
Route::get('/approved-list', function (NovaRequest $request) {
    return inertia('ApprovedCovenant');
});
Route::get('/pending-approval', 'Axistrustee\Covenants\Http\Controllers\CovenantController@pendingCovenant');
Route::get('/edit', 'Axistrustee\Covenants\Http\Controllers\CovenantController@edit');
Route::get('/clone', 'Axistrustee\Covenants\Http\Controllers\CovenantController@clone');
Route::get('/timeline/{id}', 'Axistrustee\Covenants\Http\Controllers\CovenantController@timeline');
Route::get('/summary', 'Axistrustee\Covenants\Http\Controllers\CovenantController@summary');
Route::get('/pending-approval-active', 'Axistrustee\Covenants\Http\Controllers\CovenantController@PendingApprovalActive');
Route::get('/approved-active', 'Axistrustee\Covenants\Http\Controllers\CovenantController@approvedActiveList');
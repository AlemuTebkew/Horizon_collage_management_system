<?php

use App\Http\Controllers\Dean\CourseController;
use App\Http\Controllers\Dean\ModuleController;
use App\Http\Controllers\Dean\DegreeDepartmentController;
use App\Http\Controllers\Dean\EmployeeController;
use App\Http\Controllers\Dean\FeeTypeController;
use App\Http\Controllers\Dean\ProgramController;
use App\Http\Controllers\Dean\TeacherController;
use App\Http\Controllers\Dean\TvetDepartmentController;

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/degree_departments',DegreeDepartmentController::class);
Route::apiResource('/tvet_departments',TvetDepartmentController::class);
Route::apiResource('/programs',ProgramController::class);
Route::apiResource('/teachers',TeacherController::class);
Route::apiResource('/employees',EmployeeController::class);
Route::apiResource('/courses',CourseController::class);
Route::apiResource('/modules',ModuleController::class);
Route::apiResource('/fee_types',FeeTypeController::class);
Route::post('/assign_degree_department_head', [DegreeDepartmentController::class,'assignDepartmentHead']);
Route::post('/assign_tvet_department_head', [TvetDepartmentController::class,'assignDepartmentHead']);

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});
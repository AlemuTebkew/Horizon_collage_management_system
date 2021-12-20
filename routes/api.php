<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Dean\AcademicYearController;
use App\Http\Controllers\Dean\CourseController;
use App\Http\Controllers\Dean\ModuleController;
use App\Http\Controllers\Dean\DegreeDepartmentController;
use App\Http\Controllers\Dean\EmployeeController;
use App\Http\Controllers\Dean\FeeTypeController;
use App\Http\Controllers\Dean\LevelController ;
use App\Http\Controllers\Dean\ProgramController;
use App\Http\Controllers\Dean\TeacherController;
use App\Http\Controllers\Dean\TvetDepartmentController;
use App\Http\Controllers\Dean\SemesterController;
use App\Http\Controllers\Head\DegreeSectionController;
use App\Http\Controllers\DegreeStudentFeeController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\Registrar\AddressController;
use App\Http\Controllers\Registrar\DashBoardController as RegistrarDashBoardController;
use App\Http\Controllers\Registrar\DegreeStudentController;
use App\Http\Controllers\Registrar\TvetStudentController;
use App\Http\Controllers\TvetStudentFeeController;
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

// Route::middleware('auth:sanctum', function (Request $request) {
  Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout',[Account::class,'logout']);
    Route::apiResource('/degree_sections',DegreeSectionController::class);
    Route::get('/department_courses',[CourseController::class,'getCourse']);

});
    //----------------Dean related-------------------//
Route::apiResource('/academic_years',AcademicYearController::class);
Route::post('/all_academic_years',[AcademicYearController::class,'getAllAcademicYear']);

Route::apiResource('/semesters',SemesterController::class);

Route::apiResource('/degree_departments',DegreeDepartmentController::class);
Route::apiResource('/tvet_departments',TvetDepartmentController::class);
Route::apiResource('/programs',ProgramController::class);
Route::get('/degree_programs',[ProgramController::class,'getDegreeProgram']);
Route::get('/tvet_programs',[ProgramController::class,'getTvetProgram']);
Route::apiResource('/teachers',TeacherController::class);
Route::apiResource('/employees',EmployeeController::class);
Route::get('/department_heads',[EmployeeController::class,'getDepartmentHeads']);

Route::apiResource('/courses',CourseController::class);


Route::apiResource('/modules',ModuleController::class);
Route::apiResource('/fee_types',FeeTypeController::class);
Route::post('/assign_degree_department_head', [DegreeDepartmentController::class,'assignDepartmentHead']);
Route::post('/assign_tvet_department_head', [TvetDepartmentController::class,'assignDepartmentHead']);


// });

Route::post('/login',[Account::class,'login']);


// ----------------Registrar related==========================///////
Route::apiResource('/degree_students',DegreeStudentController::class);
Route::get('/degree_students/{department_Head_id}',[DegreeStudentController::class,'getDegreeStudent']);


Route::apiResource('/tvet_students',TvetStudentController::class);
Route::apiResource('/address',AddressController::class);
Route::apiResource('/months',MonthController::class);
Route::apiResource('/levels',LevelController::class);
Route::get('/get_registrars',[EmployeeController::class,'getRegistrars']);
Route::apiResource('/dash_board',RegistrarDashBoardController::class);


Route::apiResource('/degree_student_fees',DegreeStudentFeeController::class);
Route::apiResource('/tvet_student_fees',TvetStudentFeeController::class);

/////////////////////Head////////////////////
Route::get('/student_semesters/{id}',[DegreeStudentController::class,'getStudentSemesters']);
Route::get('/student_semester_courses/{id}',[DegreeStudentController::class,'getStudentSemesterCourses']);
Route::get('/give_course_result',[DegreeStudentController::class,'giveCourseResult']);
Route::get('/degree_section_students',[DegreeSectionController ::class,'getSectionStudents']);


//------------------------cashier related--------------------------------//
Route::get('/get_cashiers',[EmployeeController::class,'getCashiers']);


Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});

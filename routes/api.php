<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Cashier\StudentFeeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Dean\AboutUsController;
use App\Http\Controllers\Dean\AcademicYearController;
use App\Http\Controllers\Dean\CourseController;
use App\Http\Controllers\Dean\ModuleController;
use App\Http\Controllers\Dean\DegreeDepartmentController;
use App\Http\Controllers\Dean\EmployeeController;
use App\Http\Controllers\Dean\EventController;
use App\Http\Controllers\Dean\FeeTypeController;
use App\Http\Controllers\Dean\LevelController ;
use App\Http\Controllers\Dean\NewsController;
use App\Http\Controllers\Dean\ProgramController;
use App\Http\Controllers\Dean\TeacherController;
use App\Http\Controllers\Dean\TvetDepartmentController;
use App\Http\Controllers\Dean\SemesterController;
use App\Http\Controllers\Dean\SocialLinkController;
use App\Http\Controllers\Dean\StudentGalaryController;
use App\Http\Controllers\Dean\UserAccountController;
use App\Http\Controllers\Dean\WebsiteController;
use App\Http\Controllers\Head\DegreeSectionController;
use App\Http\Controllers\DegreeStudentFeeController;
use App\Http\Controllers\Head\ReportController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\Registrar\AddressController;
use App\Http\Controllers\Registrar\CocController;
use App\Http\Controllers\Registrar\DashBoardController as RegistrarDashBoardController;
use App\Http\Controllers\Registrar\DegreeStudentController;
use App\Http\Controllers\Registrar\StudentCocController;
use App\Http\Controllers\Registrar\TvetStudentController;
use App\Http\Controllers\Student\DegreeStudentInfoController;
use App\Http\Controllers\Student\TvetStudentInfoController;
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

//////////////////////Authentication

    Route::post('/login',[Account::class,'login']);
    Route::post('/student_login',[Account::class,'studentLogin']);

   Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['admin','dean'])->prefix('api')->group(function () {

    });

    Route::post('/logout',[Account::class,'logout']);
    Route::apiResource('/degree_sections',DegreeSectionController::class);
    Route::get('/department_courses',[CourseController::class,'getDepartmentCourses']);
    Route::get('/degree_students_by_department',[DegreeStudentController::class,'getDegreeStudentsByDepartment']);
    Route::post('/get_course_by_semester',[ReportController::class,'getCourseTakenBySemester']);
    Route::get('/section_suggested_students',[DegreeStudentController::class,'sectionSuggestedStudents']);
    Route::get('/section_courses',[CourseController::class,'getSectionCourses']);
    Route::get('/active_teachers',[TeacherController::class,'getActiveTeacher']);
    Route::post('/assign_teacher_for_course',[CourseController::class,'assignTeacherForCourse']);
    Route::post('/grade_reports',[ReportController::class,'getGradeReport']);
    Route::post('/register_student_for_semester',[DegreeStudentController::class,'registerStudentForSemester']);
    Route::post('/register_student_for_level',[TvetStudentController::class,'registerStudentForLevel']);



    Route::apiResource('/courses',CourseController::class);

//////////////////// Reistrar Only//////////
Route::get('/students2',[DegreeStudentController::class,'getArrangedStudentsByDepartment']);
Route::get('/tvet_students2',[TvetStudentController::class,'getArrangedStudentsByDepartment']);

   });


    //----------------Dean related-------------------//
Route::apiResource('/academic_years',AcademicYearController::class);
Route::post('/all_academic_years',[AcademicYearController::class,'getAllAcademicYear']);
Route::apiResource('/degree_departments',DegreeDepartmentController::class);
Route::apiResource('/tvet_departments',TvetDepartmentController::class);
Route::apiResource('/programs',ProgramController::class);
Route::get('/degree_programs',[ProgramController::class,'getDegreeProgram']);
Route::get('/tvet_programs',[ProgramController::class,'getTvetProgram']);
Route::apiResource('/teachers',TeacherController::class);
Route::apiResource('/employees',EmployeeController::class);
Route::get('/department_heads',[EmployeeController::class,'getDepartmentHeads']);
Route::apiResource('/modules',ModuleController::class);
Route::apiResource('/fee_types',FeeTypeController::class);
Route::post('/assign_degree_department_head', [DegreeDepartmentController::class,'assignDepartmentHead']);
Route::post('/assign_tvet_department_head', [TvetDepartmentController::class,'assignDepartmentHead']);
Route::get('/get_cashiers',[EmployeeController::class,'getCashiers']);
Route::get('/get_registrars',[EmployeeController::class,'getRegistrars']);

Route::get('/employees_account',[UserAccountController::class,'getEmployees']);
Route::get('/students_account',[UserAccountController::class,'getStudents']);


//------------------Website related

Route::apiResource('/news',NewsController::class);
Route::apiResource('/events',EventController::class);
Route::apiResource('/student_galleries',StudentGalaryController::class);
Route::apiResource('/about_us',AboutUsController::class);
Route::apiResource('/social_links',SocialLinkController::class);
Route::get('/get_home_departments',[WebsiteController::class,'getDepartments']);


//  });

 Route::apiResource('/semesters',SemesterController::class);


/////////////////// start Cashier////////////////////////
Route::apiResource('/degree_student_fees',DegreeStudentFeeController::class);
Route::apiResource('/tvet_student_fees',TvetStudentFeeController::class);
Route::get('/students_paid',[StudentFeeController::class,'studentsPaid']);
Route::post('/student_tuition_detail/{student_id}',[StudentFeeController::class,'getStudentPaymentDetail']);
Route::post('/add_tuition_payment/{student_id}',[StudentFeeController::class,'addTuitionPayment']);
Route::post('/add_other_payment',[StudentFeeController::class,'addOtherPayment']);

/////////////////////Head   And  Regi////////////////
// ----------------Registrar Only==========================///////

//----------coc related
Route::apiResource('/cocs',CocController::class);
Route::post('/check_student_for_coc/{id}',[StudentCocController::class,'checkStudentForCoc']);
Route::post('/register_internal_student/{id}',[StudentCocController::class,'registerInternalStudentForCoc']);
Route::post('/register_external_student',[StudentCocController::class,'registerExternalStudentForCoc']);
Route::get('/coc_students',[StudentCocController::class,'getAllStudents']);
Route::get('/get_request_form_data/{coc_id}',[StudentCocController::class,'generateCocRequestForm']);
//--------end coc related--------


//-----------student_copy related

Route::get('/degree_student_copy',[StudentCocController::class,'']);


// -------------------end student copy
Route::apiResource('/degree_students',DegreeStudentController::class);
Route::apiResource('/tvet_students',TvetStudentController::class);
Route::apiResource('/address',AddressController::class);
Route::apiResource('/months',MonthController::class);
Route::apiResource('/levels',LevelController::class);
Route::apiResource('/dash_board',RegistrarDashBoardController::class);
Route::post('/give_course_result/{id}',[DegreeStudentController::class,'giveCourseResult']);
Route::post('/give_module_result/{id}',[TvetStudentController::class,'giveModuleResult']);
Route::get('/degree_yearly_arranged_students',[DegreeStudentController::class,'getArrangedStudents']);
Route::get('/tvet_yearly_arranged_students',[TvetStudentController::class,'getArrangedStudents']);

//-----------------------Head only----------------------------
Route::get('/degree_section_students/{section_id}',[DegreeSectionController ::class,'getSectionStudents']);
Route::post('/add_section_students',[DegreeSectionController ::class,'addStudentsToSection']);

//-----------------Both Head Registrar
Route::get('/student_levels/{id}',[TvetStudentController::class,'getStudentLevels']);
Route::get('/student_semesters/{id}',[DegreeStudentController::class,'getStudentSemesters']);
Route::get('/semester_courses/{id}',[DegreeStudentController::class,'getStudentSemesterCourses']);
Route::get('/level_modules/{id}',[TvetStudentController::class,'getStudentLevelModules']);
Route::get('/get_academic_fees',[AcademicYearController::class,'getAcademicFee']);
Route::post('/set_academic_fees/{ac_y}',[AcademicYearController::class,'setAcademicFee']);


/////////////////End Head Reg

///////////////Start Student Info/////////////////////////////

//----------------------Degree Student Info
Route::get('/degree_my_tuition/{id}',[DegreeStudentInfoController::class,'myTuition']);
Route::get('/degree_my_course/{id}',[DegreeStudentInfoController::class,'myCourse']);
Route::get('/degree_my_grade/{id}',[DegreeStudentInfoController::class,'myGrade']);
Route::get('/degree_my_status/{id}',[DegreeStudentInfoController::class,'myStatus']);
Route::get('/degree_my_coc/{id}',[DegreeStudentInfoController::class,'myCoc']);
Route::get('/degree_my_section/{id}',[DegreeStudentInfoController::class,'mySection']);

//--------------------Tvet Student Info
Route::get('/tvet_my_tuition/{id}',[TvetStudentInfoController::class,'myTuition']);
Route::get('/tvet_my_course/{id}',[TvetStudentInfoController::class,'myCourse']);
Route::get('/tvet_my_grade/{id}',[TvetStudentInfoController::class,'myGrade']);
Route::get('/tvet_my_status/{id}',[TvetStudentInfoController::class,'myStatus']);
Route::get('/tvet_my_coc/{id}',[TvetStudentInfoController::class,'myCoc']);
Route::get('/tvet_my_section/{id}',[TvetStudentInfoController::class,'mySection']);

/////////////End Student Info
Route::get('/admin_degree_students',[StudentController::class,'getDegreeStudents']);
Route::get('/admin_tvet_students',[StudentController::class,'getTvetStudents']);
Route::get('/admin_graduated_tvet_students',[StudentController::class,'getTvetGraduated']);
Route::get('/admin_graduated_degree_students',[StudentController::class,'getDegreeGraduated']);
Route::get('/admin_scholarship_tvet_students',[StudentController::class,'getTvetScholarship']);
Route::get('/admin_scholarship_degree_students',[StudentController::class,'getDegreeScholarship']);
Route::get('/admin_dashboard',[AdminDashboardController::class,'getDashboardData']);
Route::get('/admin_o_dashboard',[AdminDashboardController::class,'otherWay']);


////////////Admin Related-----//////////



///////////////End Admin------=
Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
});

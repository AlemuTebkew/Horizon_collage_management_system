<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentPaymentController;
use App\Http\Controllers\Cashier\StudentFeeController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\Dean\AboutUsController;
use App\Http\Controllers\Dean\AcademicYearController;
use App\Http\Controllers\Dean\CourseController;
use App\Http\Controllers\Dean\DeanDashboardController;
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
use App\Http\Controllers\Registrar\RegistrarDashBoardController  ;
use App\Http\Controllers\Registrar\DegreeStudentController;
use App\Http\Controllers\Registrar\StudentCocController;
use App\Http\Controllers\Registrar\StudentController as RegistrarStudentController;
use App\Http\Controllers\Registrar\TvetStudentController;
use App\Http\Controllers\Student\DegreeStudentInfoController;
use App\Http\Controllers\Student\TvetStudentInfoController;
use App\Http\Controllers\Teacher\SectionController;
use App\Http\Controllers\TvetStudentFeeController;
use App\Http\Controllers\Cashier\CashierDashboardController;
use App\Http\Controllers\Head\CourseController as HeadCourseController;
use App\Http\Controllers\Head\HeadDashboardController;
use App\Http\Controllers\Head\StudentController as HeadStudentController;
use App\Http\Controllers\Registrar\StudentApprovalController;
use App\Http\Controllers\Registrar\StudentCopyController;
use App\Http\Controllers\TvetHead\ModuleController as TvetHeadModuleController;
use App\Http\Controllers\TvetHead\StudentController as TvetHeadStudentController;
use App\Http\Controllers\TvetHead\TvetSectionController;
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
//////////////////////Authentication

    Route::post('/login',[Account::class,'login']);
    Route::post('/student_login',[Account::class,'studentLogin']);


   Route::middleware(['auth:sanctum'])->group(function () {

    //------------Password Reset
    Route::post('/change_password',[Account::class,'changePassword']);
    Route::post('/reset_employee_password',[Account::class,'resetEmployeePassword']);
    Route::post('/reset_student_password',[Account::class,'resetStudentPassword']);
    Route::post('/change_student_password',[Account::class,'changeStudentPassword']);

    //-----------End Password Related

    Route::middleware(['registrar','dean'])->prefix('')->group(function () {

    });

    Route::middleware(['admin'])->group(function () {

        //----------------------Admin Related-------------------

        Route::get('/admin_paid_students',[StudentPaymentController::class,'getPaidStudents']);
        Route::post('/admin_delete_payment/{id}',[StudentPaymentController::class,'deletePayment']);
        Route::get('/admin_o_dashboard',[AdminDashboardController::class,'otherWay']);
        Route::get('/admin_dashboard',[AdminDashboardController::class,'getDashboardData']);
        Route::get('/admin_dashboard2',[AdminDashboardController::class,'getDashboardData2']);
        Route::get('/get_deans',[EmployeeController::class,'getDeans']);
        //----------------------End Admin------=

    });

    Route::middleware(['dean'])->group(function () {

        Route::get('/employees_account',[UserAccountController::class,'getEmployees']);
        Route::get('/students_account',[UserAccountController::class,'getStudents']);
        Route::get('/dean_dashboard',[DeanDashboardController ::class,'getDashboardData']);
        Route::post('/set_academic_fees/{ac_y}',[AcademicYearController::class,'setAcademicFee']);
        Route::post('/assign_degree_department_head', [DegreeDepartmentController::class,'assignDepartmentHead']);
        Route::post('/assign_tvet_department_head', [TvetDepartmentController::class,'assignDepartmentHead']);
        Route::get('/get_cashiers',[EmployeeController::class,'getCashiers']);
        Route::get('/get_registrars',[EmployeeController::class,'getRegistrars']);
       //---------------Dean Website Related
        Route::apiResource('/news',NewsController::class);
        Route::apiResource('/events',EventController::class);
        Route::apiResource('/student_galleries',StudentGalaryController::class);
        Route::apiResource('/about_us',AboutUsController::class);
        Route::apiResource('/social_links',SocialLinkController::class);

        //----------------End Dean Functionality-------------------//

    });

    Route::middleware(['degree_head'])->group(function () {

        //-----------------------Start Head Functionality only----------------------------
             //------------------degree head--------
        Route::get('/students2',[HeadStudentController::class,'getArrangedStudentsByDepartment']);
        Route::apiResource('/degree_sections',DegreeSectionController::class);
        Route::get('/degree_section_students/{section_id}',[HeadStudentController ::class,'getSectionStudents']);
        Route::post('/add_section_students',[HeadStudentController ::class,'addStudentsToSection']);
        Route::get('/section_suggested_students',[HeadStudentController::class,'sectionSuggestedStudents']);
        Route::get('/section_courses',[HeadCourseController::class,'getSectionCourses']);

    });
    ///// --------both head
    Route::get('/head_dash_board',[HeadDashboardController::class,'getDashboardData']);
    Route::middleware(['tvet_head'])->group(function () {

    /////-------------------tvet head
    Route::get('/tvet_students2',[TvetHeadStudentController::class,'getArrangedStudentsByDepartment']);
    Route::apiResource('/tvet_sections',TvetSectionController::class);
    Route::get('/tvet_section_students/{section_id}',[TvetHeadStudentController ::class,'getSectionStudents']);
    Route::post('/tvet_add_section_students',[TvetHeadStudentController ::class,'addStudentsToSection']);
    Route::get('/section_suggested_students',[TvetHeadStudentController::class,'sectionSuggestedStudents']);
    Route::get('/section_modules',[TvetHeadModuleController::class,'getSectionModules']);

    });


    Route::middleware(['cashier'])->group(function () {

         //---------------------- Start Cashier Functionality-----------------//

        Route::get('/cashier_dashboard',[CashierDashboardController::class,'getDashboardData']);
        Route::post('/student_tuition_detail/{student_id}',[StudentFeeController::class,'getStudentPaymentDetail']);
        Route::post('/add_tuition_payment/{student_id}',[StudentFeeController::class,'addTuitionPayment']);
        Route::post('/add_other_payment',[StudentFeeController::class,'addOtherPayment']);

        //---------------------- End Cashier Functionality-----------------//
    });
    Route::post('/logout',[Account::class,'logout']);

    Route::get('/department_courses',[CourseController::class,'getDepartmentCourses']);
    Route::get('/degree_students_by_department',[DegreeStudentController::class,'getDegreeStudentsByDepartment']);
    Route::post('/get_course_by_semester',[ReportController::class,'getCourseTakenBySemester']);

    Route::get('/active_teachers',[TeacherController::class,'getActiveTeacher']);
    Route::post('/assign_teacher_for_course',[CourseController::class,'assignTeacherForCourse']);
    Route::post('/grade_reports',[ReportController::class,'getGradeReport']);
    Route::apiResource('/courses',CourseController::class);


    //----------------Start Dean Functionality-------------------//
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

    Route::get('/get_deans',[EmployeeController::class,'getDeans']);


   Route::apiResource('/semesters',SemesterController::class);

// ----------------Start Registrar Functionality==========================///////
//--------------start fee related
Route::get('/degree_paid_students',[DegreeStudentFeeController::class,'filterPaidStudentsByMonth']);
Route::get('/degree_unpaid_students',[DegreeStudentFeeController::class,'filterUnPaidStudentsByMonth']);
Route::apiResource('/tvet_student_fees',TvetStudentFeeController::class);
Route::get('/tvet_paid_students',[TvetStudentFeeController::class,'filterPaidStudentsByMonth']);
Route::get('/tvet_unpaid_students',[TvetStudentFeeController::class,'filterUnPaidStudentsByMonth']);
//---------------end fee related

//----------start coc related---------------
Route::apiResource('/cocs',CocController::class);
Route::post('/check_student_for_coc/{id}',[StudentCocController::class,'checkStudentForCoc']);
Route::post('/register_internal_student/{id}',[StudentCocController::class,'registerInternalStudentForCoc']);
Route::post('/register_external_student',[StudentCocController::class,'registerExternalStudentForCoc']);
Route::get('/get_request_form_data/{coc_id}',[StudentCocController::class,'generateCocRequestForm']);
//--------end coc related--------
//-----------student_copy related
Route::get('/degree_student_copy',[StudentCopyController::class,'']);
// -------------------end student copy
//-----------------start graduated and scholarship
Route::get('/registrar_graduated_tvet_students',[RegistrarStudentController::class,'getTvetGraduated']);
Route::get('/registrar_graduated_degree_students',[RegistrarStudentController::class,'getDegreeGraduated']);
Route::get('/registrar_scholarship_tvet_students',[RegistrarStudentController::class,'getTvetScholarship']);
Route::get('/registrar_scholarship_degree_students',[RegistrarStudentController::class,'getDegreeScholarship']);
Route::get('/registrar_dashboard',[AdminDashboardController::class,'getDashboardData']);
//-----------------end graduated and scholarship

//----------------start student registration
Route::apiResource('/degree_students',DegreeStudentController::class);
Route::apiResource('/tvet_students',TvetStudentController::class);
Route::apiResource('/months',MonthController::class);
Route::apiResource('/levels',LevelController::class);
Route::post('/give_course_result/{id}',[DegreeStudentController::class,'giveCourseResult']);
Route::post('/give_module_result/{id}',[TvetStudentController::class,'giveModuleResult']);
Route::get('/degree_yearly_arranged_students',[DegreeStudentController::class,'getArrangedStudents']);
Route::get('/tvet_yearly_arranged_students',[TvetStudentController::class,'getArrangedStudents']);
Route::post('/degree_approve',[StudentApprovalController::class,'approveDegreeStudent']);
Route::post('/tvet_approve',[StudentApprovalController::class,'approveTvetStudent']);
Route::get('/notifications/{id}',[EmployeeController::class,'get_notifications']);
Route::get('/read_notifications/{id}',[EmployeeController::class,'mark_as_read']);

// ----------------End Registrar Functionality==========================///////


//-----------------Both Head Registrar
Route::get('/student_levels/{id}',[TvetStudentController::class,'getStudentLevels']);
Route::get('/student_semesters/{id}',[DegreeStudentController::class,'getStudentSemesters']);
Route::get('/semester_courses/{id}',[DegreeStudentController::class,'getStudentSemesterCourses']);
Route::get('/level_modules/{id}',[TvetStudentController::class,'getStudentLevelModules']);
Route::post('/register_student_for_level',[TvetStudentController::class,'registerStudentForLevel']);
Route::post('/register_student_for_semester',[DegreeStudentController::class,'registerStudentForSemester']);

//-----------------------End Head Functionality only----------------------------

/////////////////End Head Reg

//------------------------Start Student Info-------------

Route::middleware(['degree_student'])->group(function () {
//----------------------Start Degree Student Info
Route::get('/degree_my_tution/{id}',[DegreeStudentInfoController::class,'myTuition']);
Route::get('/degree_my_course/{id}',[DegreeStudentInfoController::class,'myCourse']);
Route::get('/degree_my_grade/{id}',[DegreeStudentInfoController::class,'myGrade']);
Route::get('/degree_my_status/{id}',[DegreeStudentInfoController::class,'myStatus']);
Route::get('/degree_my_coc/{id}',[DegreeStudentInfoController::class,'myCoc']);
Route::get('/degree_my_section/{id}',[DegreeStudentInfoController::class,'mySection']);
});

Route::middleware(['tvet_student'])->group(function () {
//--------------------Tvet Student Info
Route::get('/tvet_my_tution/{id}',[TvetStudentInfoController::class,'myTuition']);
Route::get('/tvet_my_course/{id}',[TvetStudentInfoController::class,'myCourse']);
Route::get('/tvet_my_grade/{id}',[TvetStudentInfoController::class,'myGrade']);
Route::get('/tvet_my_status/{id}',[TvetStudentInfoController::class,'myStatus']);
Route::get('/tvet_my_coc/{id}',[TvetStudentInfoController::class,'myCoc']);
Route::get('/tvet_my_section/{id}',[TvetStudentInfoController::class,'mySection']);

//------------------------End Student Info
});

Route::middleware(['teacher'])->group(function () {
//--------------Start Teacher Functionality
Route::get('/teacher_sections/{id}',[SectionController::class,'getSections']);
Route::post('/teacher_section_students/{id}',[SectionController::class,'getTeacherStudents']);
Route::post('/teacher_set_result/{id}',[SectionController::class,'setResult']);

//--------------End Teacher Functionality
});

});//end auth_sanctum

     // --------un aunthenticated routes

     Route::get('/registrar_dash_board',[RegistrarDashBoardController::class,'getDashboardData']);
     Route::get('/coc_students',[StudentCocController::class,'getAllStudents']);
     Route::apiResource('/degree_student_fees',DegreeStudentFeeController::class);
     Route::get('/get_academic_fees',[AcademicYearController::class,'getAcademicFee']);
     Route::get('/students_paid',[StudentFeeController::class,'studentsPaid']);

           //------------------Userside Website related--------------------//

           Route::get('/get_home_departments',[WebsiteController::class,'getDepartments']);
           Route::get('/get_news',[WebsiteController::class,'getNews']);
           Route::get('/get_events',[WebsiteController::class,'getEvents']);
           Route::get('/get_student_galleries',[WebsiteController::class,'getGalleries']);
           Route::get('/get_social_links',[WebsiteController::class,'getSocialLinks']);
           Route::get('/get_academic_calenders',[AcademicYearController::class,'getAcademicCalenderActivities']);


Route::fallback(function(){
    return response()->json([
        'message' => 'Route  Not Found. If error persists, contact info@website.com'], 404);
});

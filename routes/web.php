<?php

use Illuminate\Support\Facades\Route;

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
// Route::get('/register', 'Auth\RegisterController@register')->name('register');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// USUARIOS
/** ADMINISTRADOR */
Route::name('administrator.')->prefix('administrator')
->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('home', 'AdministratorController@home' )->name('home');
    Route::get('exams', 'AdministratorController@exams' )->name('exams');
    Route::get('teachers', 'AdministratorController@teachers' )->name('teachers');
    
    Route::post('save_teacher', 'AdministratorController@save_teacher' )->name('save_teacher');
    Route::put('update_teacher', 'AdministratorController@update_teacher' )->name('update_teacher');
    Route::get('teacher_assignments', 'AdministratorController@teacher_assignments' )->name('teacher_assignments');
    Route::delete('delete_teacher', 'AdministratorController@delete_teacher' )->name('delete_teacher');
    Route::put('send_data', 'AdministratorController@send_data' )->name('send_data');
});
/** ADMINISTRADOR */
/** TEACHER */
Route::name('teacher.')->prefix('teacher')
->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('home', 'TeacherController@home' )->name('home');
    Route::get('exams', 'TeacherController@exams' )->name('exams');
    Route::get('groups', 'TeacherController@groups' )->name('groups');
    Route::get('results', 'TeacherController@results' )->name('results');
});
/** TEACHER */
/** STUDENTS */
Route::name('student.')->prefix('student')
->middleware(['auth', 'role:student'])->group(function () {
    Route::get('home', 'StudentController@home' )->name('home');
    Route::get('results/{exam_id}', 'StudentController@results' )->name('results');
});
/** STUDENTS */

// FUNCIONES
/** LEVEL */
Route::name('levels.')->prefix('levels')
->middleware(['auth'])->group(function () {
    Route::post('create', 'LevelController@create' )->name('create');
    Route::put('update', 'LevelController@update' )->name('update');
    Route::delete('delete', 'LevelController@delete' )->name('delete');
    Route::get('show', 'LevelController@show' )->name('show');

    Route::get('home', 'LevelController@home' )->name('home');
    Route::get('get_all', 'LevelController@get_all' )->name('get_all');
});
/** LEVEL */

/** TOPIC */
Route::name('topics.')->prefix('topics')
->group(function () {
    Route::get('index', 'TopicController@index' )->name('index');
    Route::get('by_level', 'TopicController@by_level' )->name('by_level');
    Route::get('show', 'TopicController@show' )->name('show');
    Route::post('create', 'TopicController@create' )->name('create');
    Route::put('update', 'TopicController@update' )->name('update');
    Route::delete('delete', 'TopicController@delete' )->name('delete');

    Route::get('instruction_assignments', 'TopicController@instruction_assignments' )->name('instruction_assignments');
});
/** TOPIC */

/** INSTRUCTION */
Route::name('instructions.')->prefix('instructions')->group(function () {
    Route::post('create', 'InstructionController@create' )->name('create');
    Route::put('update', 'InstructionController@update' )->name('update');
    Route::delete('delete', 'InstructionController@delete' )->name('delete');
    Route::put('move', 'InstructionController@move' )->name('move');
    Route::get('get_categories', 'InstructionController@get_categories' )->name('get_categories');
});
/** INSTRUCTION */

/** QUESTIONS */
Route::name('questions.')->prefix('questions')->group(function () {
    Route::get('get_types', 'QuestionController@get_types' )->name('get_types');
    Route::post('create', 'QuestionController@create' )->name('create');
    Route::put('update', 'QuestionController@update' )->name('update');
    Route::delete('delete', 'QuestionController@delete' )->name('delete');
});
/** QUESTIONS */

/** ANSWERS */
Route::name('answers.')->prefix('answers')
->group(function () {
    Route::post('create', 'AnswerController@create' )->name('create');
    Route::put('update', 'AnswerController@update' )->name('update');
});
/** ANSWERS */

/** SCHOOLS */
Route::name('schools.')->prefix('schools')
->group(function () {
    Route::get('index', 'SchoolController@index' )->name('index');
    Route::get('show', 'SchoolController@show' )->name('show');

    Route::get('show_teachers', 'SchoolController@show_teachers' )->name('show_teachers');
    Route::get('by_name', 'SchoolController@by_name' )->name('by_name');

    Route::get('by_teacher', 'SchoolController@by_teacher' )->name('by_teacher');
});
/** SCHOOLS */

/** EXAMS */
Route::name('exams.')->prefix('exams')
->group(function () {
    Route::post('create', 'ExamController@create' )->name('create');
    Route::post('save_topics', 'ExamController@save_topics' )->name('save_topics');
    Route::post('save_questions', 'ExamController@save_questions' )->name('save_questions');
    Route::put('update', 'ExamController@update' )->name('update');
    Route::put('update_topics', 'ExamController@update_topics' )->name('update_topics');
    Route::delete('delete', 'ExamController@delete' )->name('delete');
    Route::put('send', 'ExamController@send' )->name('send');
    Route::get('show', 'ExamController@show' )->name('show');
    Route::get('by_school', 'ExamController@by_school' )->name('by_school');
    Route::get('by_teacher', 'ExamController@by_teacher' )->name('by_teacher');
    Route::get('download/{exam_id}/{skills}', 'ExamController@download' )->name('download');

    // OBTENER POR NIVELES
    Route::get('enter_exam', 'ExamController@enter_exam' )->name('enter_exam');
    Route::post('start_exam', 'ExamController@start_exam' )->name('start_exam');
    Route::post('check_level', 'ExamController@check_level' )->name('check_level');
    Route::post('time_out', 'ExamController@time_out' )->name('time_out');
    
    // OBTENER DETALLES DE AVANCES
    Route::get('get_advances', 'ExamController@get_advances' )->name('get_advances');
    // Revisar si el examen tiene alumnos asignados
    Route::get('check_students', 'ExamController@check_students' )->name('check_students');

    // OBTENER TOPICS DEL EXAMEN
    Route::get('get_topics', 'ExamController@get_topics' )->name('get_topics');

    // ENVIAR NOTIFICACIÓN A CADA ALUMNO
    Route::put('notification', 'ExamController@notification' )->name('notification');

    // IR A PAGINA DE GUARDAR AUDIO
    Route::get('/go_record/{exam_id}/{question_id}', 'ExamController@go_record' )->name('go_record');
    // GUARDAR AUDIO DEL ALUMNO
    Route::post('save_record', 'ExamController@save_record' )->name('save_record');
    // GUARDAR PUNTOS
    Route::post('save_points', 'ExamController@save_points' )->name('save_points');
    // OBTENER RESULTADOS, DETALLADO
    Route::get('/get_results', 'ExamController@get_results' )->name('get_results');
    // REVISAR SKILLS
    Route::post('/check_skills', 'ExamController@check_skills' )->name('check_skills');
});
/** EXAMS */

/** GROUPS */
Route::name('groups.')->prefix('groups')
->group(function () {
    Route::post('create', 'GroupController@create' )->name('create');
    Route::put('update', 'GroupController@update' )->name('update');
    Route::delete('delete', 'GroupController@delete' )->name('delete');
    Route::get('exam_assignments', 'GroupController@exam_assignments' )->name('exam_assignments');
    
    Route::get('by_user', 'GroupController@by_user' )->name('by_user');
    Route::get('s_by_user', 'GroupController@s_by_user' )->name('s_by_user');
    Route::get('download_template', 'GroupController@download_template' )->name('download_template');
    Route::post('upload_students', 'GroupController@upload_students' )->name('upload_students');
    Route::get('list_students', 'GroupController@list_students' )->name('list_students');
    Route::put('send_access', 'GroupController@send_access' )->name('send_access');
    
    Route::post('add_student', 'GroupController@add_student' )->name('add_student');
    Route::put('update_student', 'GroupController@update_student' )->name('update_student');
    Route::get('student_assignments', 'GroupController@student_assignments' )->name('student_assignments');
    Route::delete('delete_student', 'GroupController@delete_student' )->name('delete_student');

    Route::get('list_avances', 'GroupController@list_avances' )->name('list_avances');

    Route::get('download_list/{group_id}', 'GroupController@download_list' )->name('download_list');
});
/** GROUPS */

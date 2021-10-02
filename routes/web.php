<?php

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

Route::get('/', function () {
    return redirect('/login');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/register/{code}', 'Auth\RegisterController@showRegistrationForm');


// batch
Route::get('/batch', 'BatchController@index')->name('batch');
Route::post('/batch/save', 'BatchController@save')->name('save');
Route::post('/batch/view', 'BatchController@view')->name('view');
Route::post('/batch/batchCode', 'BatchController@batchCode')->name('batchCode');

// batch master list
Route::get('/batch/masterlist/view/{batchid}', 'BatchMasterlistController@index')->name('index');
Route::post('/batch/masterlist/save', 'BatchMasterlistController@save')->name('batch.masterlist.save');
Route::post('/batch/masterlist/sende-mail-link', 'BatchMasterlistController@emailRegistrationCode')->name('batch.masterlist.email');
Route::post('/batch/masterlist/loadData', 'BatchMasterlistController@loadData')->name('batch.masterlist.loadData');
Route::post('batch/masterlist/destroy/{id}', 'BatchMasterlistController@destroy')->name('batch.masterlist.destroy');
Route::post('batch/masterlist/transfer', 'BatchMasterlistController@transfer')->name('batch.masterlist.transfer');
Route::post('/batch/send-batch/mail', 'BatchMasterlistController@sendBatchMail')->name('sendBatchMail');



// user-management
Route::get('/user-management', 'UserController@index')->name('users-management');
Route::post('/user/view', 'UserController@view')->name('users.view');
Route::post('/user/save', 'UserController@save')->name('users.save');

// student
Route::get('/student', 'StudentController@index')->name('student');
Route::get('/student/profile', 'StudentController@profile')->name('profile');
Route::post('/student/profile/profile', 'StudentController@profileUpdate')->name('profile.update');
//exams
Route::get('/student/exams', 'ExamController@exams')->name('exam');
Route::get('/student/exam/{id}', 'ExamController@exam')->name('exam.name');
Route::post('/exams/submit', 'ExamController@submit')->name('submit');
Route::post('/exam/delete', 'ExamController@delete')->name('exam.delete');


//batch excel upload
Route::get('export', 'BatchMasterlistController@export')->name('export');
Route::get('importExportView', 'BatchMasterlistController@importExportView');
Route::post('/batch/masterlist/import', 'BatchMasterlistController@import')->name('import');

//exams
Route::get('/exam', 'ExamController@index')->name('exam');
Route::post('/exam/saveExamMain', 'ExamController@saveExamMain')->name('saveExamMain');
Route::post('/exam/save', 'ExamController@save')->name('save');
Route::get('/exam/question/view/{id}', 'QuestionController@view')->name('view');
Route::get('/exam/question/view/image/{id}', 'QuestionController@viewImage')->name('view');

Route::post('/exam/details', 'ExamController@examDetails')->name('save');
Route::get('/exam/details/print_exam/{id}/{batch_id}/{exam_id}', 'ExamController@print_exam')->name('print_exam');


//REPORTS
Route::get('/exam_report', 'ExamController@viewReports')->name('reports.exam_result');
Route::post('/batch/details', 'ExamController@batchDetails');
Route::get('/batch/report/details/{id}', 'ExamController@batchReportDetails');
Route::post('/exam-result/mailto', 'ExamController@examResultEmail');
Route::get('/exam-result/cron', 'ExamResultController@index');



//batch_exam
Route::post('/batch/exam/save', 'BatchExamController@save')->name('batch.exam.save');
Route::post('/batch/exam/loadData', 'BatchExamController@loadData')->name('batch.exam.loadData');

//reviewer
Route::post('/batch/reviewer/save', 'ReviewerController@save')->name('reviewer.save');
Route::get('/batch/reviewer/view/{id}', 'ReviewerController@view')->name('reviewer.view');

Route::post('batch/reviewer/destroy/{id}', 'ReviewerController@destroy')->name('review.destroy');

// select 2   
Route::get('/select', 'BatchController@select')->name('select');



// transfer
Route::post('/batch/masterlist/save/transfer', 'BatchMasterlistController@transferConfirm')->name('select');



// image upload
// Route::get('images-upload', 'ImageExamController@imagesUpload');
Route::post('images-upload', 'ImageExamController@imagesUploadPost')->name('images.upload');
Route::post('exam/images/view', 'ImageExamController@viewImageExam');

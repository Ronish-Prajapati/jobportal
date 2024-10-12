<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\JobApplicationController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\JobsController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs',[JobController::class,'index'])->name('jobs');
Route::get('/jobs/detail/{id}',[JobController::class,'detail'])->name('jobDetail');
Route::post('/apply-job',[JobController::class,'applyJob'])->name('applyJob');
Route::post('/saved-job', [JobController::class, 'savedJob'])->name('savedJob');





Route::group(['account'], function () {
    //Guest Route
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/register', [AccountController::class, 'registration'])->name('account.registration');
        Route::post('/process-register', [AccountController::class, 'processRegistration'])->name('account.processRegistration');

        Route::get('/login', [AccountController::class, 'login'])->name('account.login');
        Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');


    });
    Route::group(['prefix' => 'admin','middleware'=>'checkRole'], function () {
        Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
        Route::get('/users',[UserController::class,'index'])->name('admin.users');
        Route::get('/users/{id}',[UserController::class,'edit'])->name('admin.users.edit');
        Route::put('/users/{id}',[UserController::class,'update'])->name('admin.users.update');
        Route::delete('/users/{id}',[UserController::class,'destroy'])->name('admin.users.delete');
        Route::get('/jobs',[JobsController::class,'index'])->name('admin.jobs');
        Route::get('/jobs/edit/{id}',[JobsController::class,'edit'])->name('admin.jobs.edit');
        Route::put('/jobs/{id}',[JobsController::class,'update'])->name('admin.jobs.update');
        Route::delete('/jobs',[JobsController::class,'destroy'])->name('admin.jobs.delete');
        Route::get('/jobapplications',[JobApplicationController::class,'index'])->name('admin.jobapplications');
        Route::delete('/job-applicaitons',[JobApplicationController::class,'destroy'])->name('admin.jobApplications.destroy');




    });
    //Auth Route
    Route::group(['middleware' => 'auth'], function () {
        
        Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');
        Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');
        Route::get('/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
        Route::post('/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
        Route::get('/my-jobs', [AccountController::class, 'myJob'])->name('account.myJobs');
        Route::get('/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
        Route::post('/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
        Route::post('/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
        Route::get('/my-job-applications', [AccountController::class, 'myJobApplications'])->name('account.myJobApplications');
        Route::put('/saved-jobs', [AccountController::class, 'savedJobs'])->name('account.savedJobs');


        Route::post('/account/remove-jobs', [AccountController::class, 'removeJobs'])->name('account.removeJobs');
        Route::post('/account/remove-saved-job', [AccountController::class, 'removeSavedJob'])->name('account.removeSavedJob');
        Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');

        
        
    });

});
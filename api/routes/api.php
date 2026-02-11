<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthController;
use Modules\User\Controllers\UserController;
use Modules\Rbac\Controllers\RbacController;
use Modules\Ubigeo\Controllers\UbigeoController;
use Modules\Office\Controllers\OfficeController;
use Modules\Office\Controllers\LocalesController;
use Modules\ProfessionalRecords\Controllers\{
    SpecializationAreaController,
    AcademicRecordController,
    CertificationController,
    JobRecordController
};
use Modules\Accounts\Controllers\{TokenController, AccountController, PersonalDataExtraController};
use Modules\JobVacancies\Controllers\{JobVacancyController, JobVacancyFileController, JobProfileController};

Route::prefix('/auth/')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt:refresh');
    Route::patch('password', [AuthController::class, 'changePassword'])->middleware('jwt:internal');
});

Route::prefix('/office/')->middleware('jwt:internal')->group(function () {
    Route::post('create', [OfficeController::class, 'creater']);
    Route::get('list', [OfficeController::class, 'lister']);
    Route::patch('update', [OfficeController::class, 'updater']);
    Route::delete('remove', [OfficeController::class, 'deleter']);

    Route::prefix('locale')->group(function () {
        Route::post('/', [LocalesController::class, 'creater']);
        Route::patch('/', [LocalesController::class, 'updater']);
        Route::get('/', [LocalesController::class, 'lister']);
    });
}); 

Route::prefix('rbac')->middleware('jwt:internal')->group(function () {
    Route::get('role/list', [RbacController::class, 'listerRole']);
    Route::post('role/create', [RbacController::class, 'createRole']);
    Route::post('role/assign', [RbacController::class, 'syncPermissionRole']);
    Route::get('permission/list', [RbacController::class, 'listerPermission']);
});

Route::prefix('/user/')->group(function () {
    Route::post('register', [UserController::class, 'register'])->middleware('jwt:internal');
    Route::get('list', [UserController::class, 'list'])->middleware('jwt:internal');
    Route::patch('update', [UserController::class, 'update'])->middleware('jwt:internal');
    Route::post('change-password', [UserController::class, 'changePassword'])->middleware('jwt:internal');
});

Route::prefix('ubigeo')->group(function () {
    Route::get('departments', [UbigeoController::class, 'departments']);
    Route::get('province', [UbigeoController::class, 'provinces']);
    Route::get('districts',  [UbigeoController::class,  'districts']);
});

Route::prefix('accounts')->group(function () {
    Route::get('token', [TokenController::class, 'generate'])->middleware('anti.bot:token_request,3,6');
    Route::post('register', [AccountController::class, 'register'])->middleware('anti.bot:register_action,6,2');

    Route::prefix('personal-data')->middleware('jwt:internal')->group(function () {
        Route::post('/', [PersonalDataExtraController::class, 'upsert']);
        Route::get('/', [PersonalDataExtraController::class, 'show']);
        Route::get('certificate/{certificateType}',[PersonalDataExtraController::class, 'downloadCertificate'])
            ->where('certificateType', '.*')
            ->name('personal-data.certificate');
    });
});

Route::prefix('professional-records')->middleware('jwt:internal')->group(function () {
    Route::prefix('specialization-areas')->group(function () {
    Route::get('/', [SpecializationAreaController::class, 'list'])
            ->withoutMiddleware('jwt:internal');
        Route::post('/', [SpecializationAreaController::class, 'create']);
        Route::patch('/', [SpecializationAreaController::class, 'update']);
    });

    Route::prefix('academic-records')->group(function () {
        Route::get('/', [AcademicRecordController::class, 'list']);
        Route::post('/', [AcademicRecordController::class, 'create']);
        Route::post('update/', [AcademicRecordController::class, 'update']);
        Route::delete('/', [AcademicRecordController::class, 'delete']);
    });

    Route::prefix('certifications')->group(function () {
        Route::get('/', [CertificationController::class, 'list']);
        Route::post('/', [CertificationController::class, 'create']);
        Route::post('/update', [CertificationController::class, 'update']);
        Route::delete('/', [CertificationController::class, 'delete']);
    });

    Route::prefix('job-records')->group(function () {
        Route::get('/', [JobRecordController::class, 'list']);
        Route::post('/', [JobRecordController::class, 'create']);
        Route::post('/update', [JobRecordController::class, 'update']);
        Route::delete('/', [JobRecordController::class, 'delete']);
    });

    Route::get('files/{filePath}', [\Modules\ProfessionalRecords\Controllers\SharedController::class, 'GetFile'])
        ->where('filePath', '.*')
        ->name('professional-records.files.download');
});

// Job Vacancies routes
Route::prefix('job-vacancies')->group(function () {
    // Public routes
    Route::get('/', [JobVacancyController::class, 'list']);
    Route::get('/show', [JobVacancyController::class, 'show']);
    Route::get('/files/download/{filePath}', [JobVacancyFileController::class, 'download'])
        ->where('filePath', '.*')
        ->name('job-vacancies.files.download');
    
    // Protected routes (admin only)
    Route::middleware('jwt:internal')->group(function () {
        Route::post('/', [JobVacancyController::class, 'create']);
        Route::patch('/', [JobVacancyController::class, 'update']);
        Route::patch('/status', [JobVacancyController::class, 'updateStatus']);
        
        Route::post('/files', [JobVacancyFileController::class, 'attach']);
        Route::patch('/files', [JobVacancyFileController::class, 'updateName']);
        
        Route::post('/profiles', [JobProfileController::class, 'manage']);
    });
});
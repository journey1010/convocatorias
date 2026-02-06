<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthController;
use Modules\User\Controllers\UserController;
use Modules\Rbac\Controllers\RbacController;
use Modules\Ubigeo\Controllers\UbigeoController;
use Modules\Office\Controllers\OfficeController;
Use Modules\Accounts\Controllers\{TokenController, AccountController, PersonalDataExtraController};

Route::prefix('/auth/')->group(function(){
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt:refresh');
    Route::patch('password', [AuthController::class, 'changePassword'])->middleware('jwt:internal');
});

Route::prefix('/office/')->group(function(){
    Route::post('create', [OfficeController::class, 'creater'])->middleware('jwt:internal');
    Route::get('list', [OfficeController::class, 'lister'])->middleware('jwt:internal');
    Route::patch('update', [OfficeController::class, 'updater'])->middleware('jwt:internal');
    Route::delete('remove', [OfficeController::class, 'deleter'])->middleware('jwt:internal');
});

Route::prefix('rbac')->group(function(){
    Route::get('role/list', [RbacController::class, 'listerRole'])->middleware('jwt:internal');
    Route::post('role/create', [RbacController::class, 'createRole'])->middleware('jwt:internal');
    Route::post('role/assign', [RbacController::class, 'syncPermissionRole'])->middleware('jwt:internal');
    Route::get('permission/list', [RbacController::class, 'listerPermission'])->middleware('jwt:internal');
});

Route::prefix('/user/')->group(function(){
    Route::post('register', [UserController::class, 'register'])->middleware('jwt:internal');
    Route::get('list', [UserController::class, 'Lister'])->middleware('jwt:internal');
    Route::patch('update', [UserController::class, 'updater'])->middleware('jwt:internal');
});

Route::prefix('ubigeo')->group(function(){
    Route::get('departments', [UbigeoController::class , 'departments']);
    Route::get('province', [UbigeoController::class, 'provinces']);
    Route::get('districts',  [UbigeoController::class,  'districts']);
});

Route::prefix('accounts')->group(function(){
    Route::get('token', [TokenController::class, 'generate'])->middleware('anti.bot:token_request,3,6');
    Route::get('register', [AccountController::class, 'register'])->middleware('anti.bot:register_action,6,2');
    
    Route::prefix('personal-data')->middleware('jwt:internal')->group(function(){
        Route::post('/', [PersonalDataExtraController::class, 'upsert']);
        //Route::get('/', [PersonalDataExtraController::class, 'show']);
        Route::get('certificate/{certificateType}', [PersonalDataExtraController::class, 'downloadCertificate']);
    });
});
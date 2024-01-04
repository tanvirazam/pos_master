<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\TokenVerificationMiddleware;


//Web Api route
Route::post('/user-registration', [UserController::class, 'userRegistration']);
Route::post('/user-login', [UserController::class, 'userLogin']);
Route::post('/mail-send', [UserController::class, 'OTPMailSendCode']);
Route::post('/verify-otp', [UserController::class, 'VerifayOtp']);
Route::post('/password-reset', [UserController::class, 'passwordReset'])->middleware([TokenVerificationMiddleware::class]);
Route::get('/user-profile', [UserController::class, 'userProfile'])->middleware([TokenVerificationMiddleware::class]);
Route::post('/user-update', [UserController::class, 'userProfileUpdate'])->middleware([TokenVerificationMiddleware::class]);
//Web Api route end
// logout
Route::get('/logout', [UserController::class, 'userLogout']);

//web page route
Route::get('/userLogin', [UserController::class, 'LoginPage']);
Route::get('/userRegistration', [UserController::class, 'RegistrationPage']);
Route::get('/sendOtp', [UserController::class, 'SendOtpPage']);
Route::get('/verifyOtp', [UserController::class, 'VerifyOTPPage']);
Route::get('/resetPassword', [UserController::class, 'ResetPasswordPage'])->middleware([TokenVerificationMiddleware::class]);
Route::get('/dashboard', [DashboardController::class, 'DashboardPage'])->middleware([TokenVerificationMiddleware::class]);
Route::get('/profile-page', [UserController::class, 'ProfileForm'])->middleware([TokenVerificationMiddleware::class]);
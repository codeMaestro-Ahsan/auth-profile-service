<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\EmailVerificationController;


Route::post('/register',[AuthController::class,'register']);
Route::get('/email/verify/{id}/{hash}',[EmailVerificationController::class,'verify'])
->name('verification.verify');
Route::post('/login',[AuthController::class,'login']);
Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
Route::post('/reset-password',[AuthController::class,'resetPassword']);


Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/email/verification-notification',[EmailVerificationController::class,'send'])->name('verification.send');

    Route::get('/profile',[ProfileController::class,'show']);
    Route::put('/profile',[ProfileController::class, 'update']);
    Route::delete('/profile',[ProfileController::class, 'destroy']);
    
    Route::get('/user',[AuthController::class,'show']);
    Route::put('/user/{user}',[AuthController::class,'update']);
    Route::delete('/user/{user}',[AuthController::class,'destroy']);
    
    Route::post('/logout',[AuthController::class, 'logout']);
});
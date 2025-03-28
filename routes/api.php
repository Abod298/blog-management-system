<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json(new UserResource($request->user()));
    });
    Route::get('user/permissions' , [UserController::class , 'getUserPermissions']);

    Route::apiResource('categories' , CategoryController::class);

    Route::get('get-related-posts' , [PostController::class , 'getRelatedPosts']);
    Route::apiResource('posts' , PostController::class);

    Route::get('get-unconfirmed-comments', [CommentController::class, 'getUnconfirmedComments']);
    Route::get('get-related-comments', [CommentController::class, 'getRelatedComments']);
    Route::post('comments/{comment}/confirm', [CommentController::class, 'confirm']);
    Route::apiResource('comments' , CommentController::class);

    Route::apiResource('users' , UserController::class);
    Route::apiResource('permissions' , PermissionController::class);



});

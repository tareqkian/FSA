<?php

use Illuminate\Support\Facades\Route;
use Tarek\Fsa\Http\Controllers\FSAController;

Route::group(["prefix" => "/auth", "middleware" => ["guest"]], function (object $route) {
    $route->post("/", [FSAController::class, "login"]);
    $route->post("/register", [FSAController::class, "register"]);
    $route->post('/mobile', [FSAController::class, "mobileLogin"]);
    $route->post('/forgot-password', [FSAController::class, "forgotPassword"])->name('password.email');

    $route->get('/reset-password/{token}', fn (string $token) => ['token' => $token])->name('password.reset');

    $route->post('/reset-password', [FSAController::class, 'resetPassword'])->name('password.update');

    $route->get('/{provider}', [FSAController::class,'redirectToProvider']);
    $route->get('/{provider}/callback', [FSAController::class,'handleProviderCallback']);
});

Route::group(["middleware" => ['auth:sanctum', 'verified']], function (object $route) {
    $route->post("/logout", [FSAController::class, "logout"]);
});

Route::group(["middleware" => ["signed"]], function(object $route) {
    $route->get('/email/verify/{id}/{hash}', [FSAController::class, 'emailVerification'])->name('verification.verify');
});

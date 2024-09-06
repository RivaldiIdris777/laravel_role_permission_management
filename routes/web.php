<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleHasPermissionController;
use App\Http\Controllers\TwoFactorController;

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
    return view('auth.login2');
});

// Verify OTP
Route::resource('verify_otp', TwoFactorController::class);

Route::middleware('auth','two_factor')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/user/logout', [UserController::class, 'LogoutUser'])->name('user.logout');

    Route::controller(UserController::class)->group(function() {
        Route::get('/dashboard', 'Dashboard')->name('dashboard');
        Route::get('/all/users', 'AllUser')->name('all.user')->middleware('permission:user.all');
        Route::get('/add/user', 'AddUser')->name('add.user')->middleware('permission:user.add');
        Route::post('/store/user', 'StoreUser')->name('user.store')->middleware('permission:user.store');
        Route::get('/edit/user/{id}', 'EditUser')->name('edit.user')->middleware('permission:user.edit');
        Route::post('/update/user', 'UpdateUser')->name('user.update')->middleware('permission:user.update');
        Route::get('/delete/user/{id}', 'DeleteUser')->name('delete.user');
    });

    Route::resource('roles', RoleController::class);

    Route::controller(PermissionController::class)->group(function() {
        Route::get('/all/permission', 'AllPermission')->name('all.permission');
        Route::get('/add/permission', 'AddPermission')->name('add.permission');
        Route::post('/store/permission', 'StorePermission')->name('permission.store');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
        Route::post('/update/permission', 'UpdatePermission')->name('permission.update');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');
    });

    Route::controller(RoleHasPermissionController::class)->group(function() {
        Route::get('/all/rolepermission', 'AllRolePermission')->name('all.rolepermission');
        Route::get('/add/rolepermission', 'AddRolesPermission')->name('add.rolepermission');
        Route::post('/store/rolepermission', 'StoreRolesPermission')->name('rolepermission.store');
        Route::get('/edit/rolepermission/{id}', 'EditRolesPermission')->name('edit.rolepermission');
        Route::post('/update/rolepermission/{id}', 'UpdateRolePermission')->name('rolepermission.update');
        Route::get('/delete/rolepermission/{id}','DeleteRolePermission')->name('rolepermission.delete');
    });

});

require __DIR__.'/auth.php';

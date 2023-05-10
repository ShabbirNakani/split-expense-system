<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Models\GroupList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect('home');
        } else {
            return redirect('login');
        }
    });

    //for groups
    Route::resource('/groups', GroupController::class);
    Route::get('/check-expenses-before-edit', [GroupController::class, 'checkExpensesBeforeEdit'])->name('checkExpensesBeforeEdit');

    // for show groups
    Route::resource('/expense', ExpenseController::class);

    // for frinds
    Route::resource('/friends', FriendsController::class);

    // edit prifile
    Route::get('/editProfile', function () {
        return view('editProfile');
    })->name('edit.profile');

    // updateProfile controller
    Route::post('/updateProfile', [HomeController::class, 'updateProfile'])->name('update.profile');

    // settel Modal Data route
    Route::get('/settel-modal-data', [FriendsController::class, 'settelModalData'])->name('settel.modal.data');
    Route::get('/settel-expense', [FriendsController::class, 'settelExpense'])->name('settel.expense');
});

// testing purpose
Route::get('/data-table', function () {
    return view('dataTableTest');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

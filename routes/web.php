<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\Admin\MenuItemController;


// ======================================================
// EMAIL
// ======================================================

Route::get('/test-email', function () {
    Mail::raw(
        'This is a test email from Lolay\'s Catering Services.',
        function ($message) {
            $message
                ->to('juniorcmch58@gmail.com')
                ->subject('Lolay\'s Catering Services - Test Email');
        }
    );

    return 'Test email sent!';
});

Route::post(
    '/admin/reservation/{reservation}/send-confirmation',
    [ReservationController::class, 'sendConfirmation']
)->name('admin.reservation.sendConfirmation');


// ======================================================
// PUBLIC PAGES
// ======================================================

Route::get('/homepage', function () {
    return view('homepage');
})->name('home');

Route::get('/about-us', function () {
    return view('about-us');
})->name('about-us');

Route::get('/package', function () {
    return view('package');
})->name('package');

Route::get('/adds-on-package', function () {
    return view('adds-on-package');
})->name('add-ons');


// ======================================================
// ADMIN DASHBOARD
// ======================================================


Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.dashboard');


// ======================================================
// ADMIN RESERVATION
// ======================================================

Route::middleware(['auth'])->group(function () {

    // Display reservations
    Route::get(
        '/admin/reservation',
        [ReservationController::class, 'index']
    )->name('admin.reservation');

    // Create reservation
    Route::post(
        '/admin/reservation',
        [ReservationController::class, 'store']
    )->name('admin.reservation.store');

    // Update reservation
    Route::put(
        '/admin/reservation/{reservation}',
        [ReservationController::class, 'update']
    )->name('admin.reservation.update');

    // Cancel reservation
    Route::patch(
        '/admin/reservation/{reservation}/cancel',
        [ReservationController::class, 'cancel']
    )->name('admin.reservation.cancel');

    // Delete reservation
    Route::delete(
        '/admin/reservation/{reservation}',
        [ReservationController::class, 'destroy']
    )->name('admin.reservation.destroy');
});


// ======================================================
// ADMIN CALENDAR
// ======================================================

Route::get('/admin/calendar', function () {
    return view('admin.calendar');
})->middleware(['auth'])->name('admin.calendar');


// ======================================================
// ADMIN PACKAGES
// ======================================================

Route::middleware('auth')->group(function () {

    Route::resource('admin/packages', PackageController::class)
        ->names([
            'index'   => 'admin.packages',
            'create'  => 'admin.packages.create',
            'store'   => 'admin.packages.store',
            'show'    => 'admin.packages.show',
            'edit'    => 'admin.packages.edit',
            'update'  => 'admin.packages.update',
            'destroy' => 'admin.packages.destroy',
        ]);

});


// ======================================================
// ADMIN MENU
// ======================================================

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/menu', [MenuItemController::class, 'index'])->name('admin.menu');
    Route::get('/admin/menu/add', [MenuItemController::class, 'create'])->name('admin.menu.add');
    Route::post('/admin/menu', [MenuItemController::class, 'store'])->name('admin.menu.store');
    Route::get('/admin/menu/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('admin.menu.edit');
    Route::put('/admin/menu/{menuItem}', [MenuItemController::class, 'update'])->name('admin.menu.update');
    Route::delete('/admin/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('admin.menu.destroy');
});

Route::get('/admin/menu/add', function () {
    return view('admin.add-menu');
})->middleware(['auth'])->name('admin.menu.add');

// ======================================================
// ADMIN CUSTOMER
// ======================================================

Route::get('/admin/customer', function () {
    return view('admin.customer');
})->middleware(['auth'])->name('admin.customer');


// ======================================================
// PROFILE
// ======================================================

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});


// ======================================================
// TODO / TASKS
// ======================================================

Route::middleware('auth')->group(function () {

    Route::get(
        '/todo',
        [TaskController::class, 'index']
    )->name('tasks.index');

    Route::post(
        '/todo',
        [TaskController::class, 'store']
    )->name('tasks.store');

    Route::put(
        '/todo/{task}',
        [TaskController::class, 'update']
    )->name('tasks.update');

    Route::delete(
        '/todo/{task}',
        [TaskController::class, 'destroy']
    )->name('tasks.destroy');

    Route::get(
        '/todo/{task}/complete',
        [TaskController::class, 'complete']
    )->name('tasks.complete');

    Route::get(
        '/todo/{task}/start',
        [TaskController::class, 'start']
    )->name('tasks.start');

    Route::get(
        '/todo-months',
        [TaskController::class, 'monthData']
    )->name('tasks.months');

});


// ======================================================
// AUTH ROUTES
// ======================================================

require __DIR__.'/auth.php';
Route::get('/test-sms', function (SmsService $smsService) {

    $smsService->send(
        '639XXXXXXXXX',
        'Test SMS from Lolays Catering Services.'
    );

    return 'SMS sent successfully!';
});




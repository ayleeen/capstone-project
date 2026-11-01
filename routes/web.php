<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Client\ReservationController as ClientReservationController;
use App\Http\Controllers\Admin\ArchiveController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Models\Package;
use App\Http\Controllers\Admin\BusinessProfileController;
use App\Http\Controllers\Auth\PhoneLoginController;
use App\Http\Controllers\Client\MenuController;
// use App\Http\Controllers\Auth\LoginOtpController;

//another way to login
Route::post('/login/phone/send', [PhoneLoginController::class, 'sendCode'])
    ->middleware(['guest', 'throttle:10,1'])
    ->name('login.phone.send');

Route::post('/login/phone/verify', [PhoneLoginController::class, 'verifyCode'])
    ->middleware(['guest', 'throttle:10,1'])
    ->name('login.phone.verify');


//notif
Route::post('/notifications/read-all', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->middleware('auth')->name('notifications.readAll');
// ======================================================
// EMAIL
// ======================================================

//menu controller customize

// Route::get('/reservation/customize-menu', [MenuController::class, 'customize'])
//     ->name('client.customize-menu');


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

//profile user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');
});


// ======================================================
// LOGIN VIA EMAIL OTP
// ======================================================

// Route::middleware('guest')->group(function () {
//     Route::get('/login/otp', [LoginOtpController::class, 'showRequest'])->name('login.otp');
//     Route::post('/login/otp', [LoginOtpController::class, 'sendCode'])->name('login.otp.send');

//     Route::get('/login/otp/verify', [LoginOtpController::class, 'showVerify'])->name('login.otp.verify');
//     Route::post('/login/otp/verify', [LoginOtpController::class, 'verify'])->name('login.otp.check');
//     Route::post('/login/otp/resend', [LoginOtpController::class, 'resend'])->name('login.otp.resend');
// });

Route::post(
    '/admin/reservation/{reservation}/send-confirmation',
    [ReservationController::class, 'sendConfirmation']
)->name('admin.reservation.sendConfirmation');

Route::get('/admin/calendar', [CalendarController::class, 'index'])
    ->middleware(['auth'])->name('admin.calendar');


// ======================================================
// PUBLIC PAGES
// ======================================================

Route::get('/homepage', function () {
    $packages = Package::where('status', true)
        ->orderBy('category')
        ->orderBy('price')
        ->take(6)
        ->get();

    return view('homepage', compact('packages'));
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

Route::get('/menu', function () {
    return view('menu');
})->name('menu');

Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
//logo
//   Route::get('/logo', function () {
//       return view('admin.logo');
//   })->name('admin.logo');



Route::get('/logo', [BusinessProfileController::class, 'edit'])->name('admin.logo');
Route::post('/logo', [BusinessProfileController::class, 'update'])->name('admin.logo.update');

// ======================================================
// ADMIN DASHBOARD
// ======================================================


Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.dashboard');


//business profile


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

Route::get('/admin/calendar', [CalendarController::class, 'index'])
    ->middleware(['auth'])->name('admin.calendar');

Route::get('/admin/reports', function () {
    return view('admin.reports');
})->middleware(['auth'])->name('admin.reports');

Route::get('/admin/notifications', function () {
    return view('admin.notifications');
})->middleware(['auth'])->name('admin.notifications');

Route::get('/admin/profile', function () {
    return view('admin.profile');
})->middleware(['auth'])->name('admin.profile');

Route::get('/admin/payment', function () {
    return view('admin.payment');
})->middleware(['auth'])->name('admin.payment');

Route::get('/admin/security', function () {
    return view('admin.security');
})->middleware(['auth'])->name('admin.security');



Route::get('/admin/audit', [\App\Http\Controllers\Admin\AuditController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.audit');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/archive', [ArchiveController::class, 'index'])->name('admin.archive');
    Route::post('/admin/archive/{tab}/restore', [ArchiveController::class, 'restore'])->name('admin.archive.restore');
    Route::post('/admin/archive/{tab}/delete', [ArchiveController::class, 'destroy'])->name('admin.archive.delete');
});

//reports


Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
Route::get('/admin/reports/data', [ReportController::class, 'data'])->name('admin.reports.data');

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

//settings

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
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



// ======================================================
// ADMIN CUSTOMER
// ======================================================

Route::get('/admin/customer', [CustomerController::class, 'index'])->middleware(['auth'])->name('admin.customer');
Route::post('/admin/customers', [CustomerController::class, 'store'])->middleware(['auth'])->name('admin.customers.store');
Route::put('/admin/customers/{customer}', [CustomerController::class, 'update'])->middleware(['auth'])->name('admin.customers.update');
Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy'])->middleware(['auth'])->name('admin.customers.destroy');


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

//customer
//customer

Route::middleware('auth')->group(function () {

    Route::get('/reservation-process-1', [ClientReservationController::class, 'process1'])
        ->name('reservation.process1');
    Route::post('/reservation-process-1', [ClientReservationController::class, 'storeProcess1'])
        ->name('reservation.process1.store');

    Route::get('/reservation-process-2', [ClientReservationController::class, 'process2'])
        ->name('reservation.process2');
    Route::post('/reservation-process-2', [ClientReservationController::class, 'storeProcess2'])
        ->name('reservation.process2.store');

    Route::get('/reservation-process-3', [ClientReservationController::class, 'process3'])
        ->name('reservation.process3');

    Route::get('/reservation/package-menu', [ClientReservationController::class, 'packageMenu'])
    ->name('reservation.package-menu');
    Route::post('/reservation/package-menu', [ClientReservationController::class, 'storePackageMenu'])
    ->name('reservation.package-menu.store');
    Route::get('/reservation/process-4', [ClientReservationController::class, 'process4'])
    ->name('reservation.process4');

    Route::get('/reservation/process-5', [ClientReservationController::class, 'process5'])
    ->name('reservation.process5');
    Route::post('/reservation/cancel', [ClientReservationController::class, 'cancel'])
    ->name('reservation.cancel');

    Route::post('/reservation/confirm', [ClientReservationController::class, 'confirm'])
    ->name('reservation.confirm');
        Route::get('/reservation/done', [ClientReservationController::class, 'done'])
        ->name('reservation.done');
            Route::get('/reservation/customize-menu', [MenuController::class, 'customize'])
        ->name('reservation.customize-menu');
Route::post('/reservation/customize-menu', [ClientReservationController::class, 'storeCustomMenu'])
    ->name('reservation.customize-menu.store');
    Route::get('/admin/addon-prices', [\App\Http\Controllers\Admin\AddonPriceController::class, 'index'])->name('admin.addon-prices');
Route::put('/admin/addon-prices', [\App\Http\Controllers\Admin\AddonPriceController::class, 'update'])->name('admin.addon-prices.update');
});


//packages controller

Route::get('/package', [\App\Http\Controllers\Client\PackageController::class, 'index'])->name('package');

//menu controller
Route::get('/menu', [\App\Http\Controllers\Client\MenuController::class, 'index'])->name('menu');

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




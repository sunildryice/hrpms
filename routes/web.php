<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SampleController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/info', function () {
    return 'Server Connected';
 // return  phpinfo();
});

Route::get('/timezone', function () {
    return date_default_timezone_get();;
//    phpinfo();
});

Route::middleware(['logger'])->group(function () {
    Route::get('/', [LoginController::class, 'login'])->name('signin');
    Route::get('login', [LoginController::class, 'login'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('auth.login');

    Route::get('forget/password', [ForgetPasswordController::class, 'create'])->name('forget.password.create');
    Route::post('forget/password', [ForgetPasswordController::class, 'store'])->name('forget.password.store');
    Route::get('reset/password/{token}', [ResetPasswordController::class, 'create'])->name('reset.password.create');
    Route::post('rest/password/{token}', [ResetPasswordController::class, 'store'])->name('reset.password.store');
});

Route::middleware(['auth', 'logger'])->group(function () {

    Route::get('loginas/{user}', [LoginController::class, 'loginas'])->name('loginas.user');
    Route::get('login/as/original', [LoginController::class, 'loginasOriginal'])->name('loginas.original.user');
    Route::get('logout', [LoginController::class, 'logout'])->name('auth.logout');
    Route::get('change/password', [ChangePasswordController::class, 'create'])->name('change.password.create');
    Route::post('change/password', [ChangePasswordController::class, 'store'])->name('change.password.store');
});

Route::middleware(['auth', 'logger'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('audit/logs', [AuditLogController::class, 'index'])->name('audit.logs.index');
    Route::get('audit/logs/{log}', [AuditLogController::class, 'show'])->name('audit.logs.show');
    Route::get('activity/logs', [ActivityLogController::class, 'index'])->name('activity.logs.index');
    Route::get('email/logs', [EmailLogController::class, 'index'])->name('email.logs.index');
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{url}', [NotificationController::class, 'show'])->name('notifications.show');
});

Route::middleware(['auth', 'logger'])->group(function () {
    Route::get('employee/code/check/create', [SampleController::class, 'importPage'])->name('employee.code.check.create');
    Route::post('employee/code/check', [SampleController::class, 'import'])->name('employee.code.check');
});



Route::prefix('mailable')->group(function () {
    Route::get('invitation/{id}', function () {
        $employee = \Modules\Employee\Models\Employee::find(2);
        return new \Modules\Employee\Mail\SendInvitation($employee);
    });
});
Route::get('dashboardnew', function () {
    return view('dashboardnew');
})->name('design.hr.dashboardnew');

Route::prefix('design/hr')->group(function () {
    Route::get('employee-registration', function () {
        return view('design.employee-registration');
    })->name('design.hr.employee-registration');
    Route::get('employee-list', function () {
        return view('design.employee-list');
    })->name('design.hr.employee-list');
    Route::get('employee-details', function () {
        return view('design.employee-profile');
    })->name('design.hr.employee-details');

    Route::get('status-list', function () {
        return view('design.status-list');
    })->name('design.hr.status-list');

    Route::get('leave-setup', function () {
        return view('design.leave-setup');
    })->name('design.hr.leave-setup');
    Route::get('vehicle', function () {
        return view('design.vehicle');
    })->name('design.hr.vehicle');
    Route::get('probationary', function () {
        return view('design.probationary');
    })->name('design.hr.probationary');
    Route::get('worklog', function () {
        return view('design.worklog');
    })->name('design.hr.worklog');
    Route::get('advance-request', function () {
        return view('design.advance-request');
    })->name('design.hr.advance-request');

    Route::get('employee-advance', function () {
        return view('design.employee-advance');
    })->name('design.hr.employee-advance');
    Route::get('maintenance', function () {
        return view('design.maintenance');
    })->name('design.hr.maintenance');
    Route::get('employee-exit', function () {
        return view('design.employee-exit');
    })->name('design.hr.employee-exit');
    Route::get('hrtraining', function () {
        return view('design.hrtraining');
    })->name('design.hr.hrtraining');
    Route::get('memo', function () {
        return view('design.memo');
    })->name('design.hr.memo');

    Route::get('employee/requisition', function () {
        return view('design.employee-requisition');
    })->name('design.hr.employee.requisition');

    Route::get('vehicle/request', function () {
        return view('design.vehicle-view');
    })->name('design.hr.vehicle-request');
});



Route::prefix('design/print')->group(function () {
    Route::get('advance/settlement', function () {
        return view('design.printsettlement');
    })->name('design.print.advance.settlement');

    Route::get('training/report', function () {
        return view('design.training-reportPrint');
    })->name('design.print.training.report');

    Route::get('training/request', function () {
        return view('design.training-requestPrint');
    })->name('design.print.training.request');

    Route::get('probation/review', function () {
        return view('design.probation-reviewPrint');
    })->name('design.print.probation.review');

    Route::get('worklog', function () {
        return view('design.work-logPrint');
    })->name('design.print.worklog');

    Route::get('exit/interview-form', function () {
        return view('design.exit-interviewformPrint');
    })->name('design.print.exit.interview-form');

    Route::get('transport', function () {
        return view('design.transportprint');
    })->name('design.print.transport');

    Route::get('memo', function () {
        return view('design.memoprint');
    })->name('design.print.memo');

    Route::get('staff', function () {
        return view('design.staffprint');
    })->name('design.print.staff');

    Route::get('purchase/order', function () {
        return view('design.poprint');
    })->name('design.print.purchase.order');

    Route::get('fund-request', function () {
        return view('design.fund-request');
    })->name('design.print.fund-request');


    Route::get('advancerequest', function () {
        return view('design.advancerequest');
    })->name('design.print.advancerequest');

    Route::get('travelreport', function () {
        return view('design.travelreport');
    })->name('design.print.travelreport');

    Route::get('travelclaim', function () {
        return view('design.travelclaim');
    })->name('design.print.travelclaim');

    Route::get('paymentsheet', function () {
        return view('design.paymentsheet');
    })->name('design.print.paymentsheet');

    Route::get('staffpersonalinformation', function () {
        return view('design.staffpersonalinformation');
    })->name('design.print.staffpersonalinformation');

    Route::get('vehiclerequest', function () {
        return view('design.vehiclerequest');
    })->name('design.print.vehiclerequest');

    Route::get('employeerequisition', function () {
        return view('design.employeerequisition');
    })->name('design.print.employeerequisition');
    Route::get('staffattendance', function () {
        return view('design.staffattendance');
    })->name('design.print.staffattendance');


    Route::get('staffattendanceprint', function () {
        return view('design.staffattendanceprint');
    })->name('design.print.staffattendanceprint');

    Route::get('mtperformance', function () {
        return view('design.mtperformance');
    })->name('design.print.mtperformance');

    Route::get('annualperformance', function () {
        return view('design.annualperformance');
    })->name('design.print.annualperformance');

    Route::get('fieldvisitreport', function () {
        return view('design.fieldvisitreport');
    })->name('design.print.fieldvisitreport');

    Route::get('travelauthorization', function () {
        return view('design.travelauthorization');
    })->name('design.print.travelauthorization');

    Route::get('leaverequest', function () {
        return view('design.leaverequest');
    })->name('design.print.leaverequest');
});

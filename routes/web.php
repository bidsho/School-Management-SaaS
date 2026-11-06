<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Models\School;

// School dashboard
use App\Livewire\School\Dashboard\SchoolDashboard;

// Attendance, exams and payments
use App\Livewire\School\Attendance\AttendanceIndex;
use App\Livewire\School\Exams\ExamIndex;
use App\Livewire\School\Payments\PaymentIndex;

// Students
use App\Livewire\School\Students\StudentCreate;
use App\Livewire\School\Students\StudentEdit;
use App\Livewire\School\Students\StudentIndex;
use App\Livewire\School\Students\StudentShow;

// Teachers
use App\Livewire\School\Teachers\TeacherIndex;
use App\Livewire\School\Teachers\TeacherCreate;
use App\Livewire\School\Teachers\TeacherEdit;
use App\Livewire\School\Teachers\TeacherShow;

// Enrollments
use App\Livewire\School\Enrollments\EnrollmentIndex;
use App\Livewire\School\Enrollments\EnrollmentCreate;
use App\Livewire\School\Enrollments\EnrollmentEdit;

// Classes
use App\Livewire\School\Classes\ClassIndex;
use App\Livewire\School\Classes\ClassCreate;
use App\Livewire\School\Classes\ClassEdit;

// Sections
use App\Livewire\School\Sections\SectionIndex;
use App\Livewire\School\Sections\SectionCreate;
use App\Livewire\School\Sections\SectionEdit;

// Parents
use App\Livewire\School\Parents\ParentIndex;
use App\Livewire\School\Parents\ParentCreate;
use App\Livewire\School\Parents\ParentEdit;

// Academic sessions
use App\Livewire\School\AcademicSessions\AcademicSessionIndex;
use App\Livewire\School\AcademicSessions\AcademicSessionCreate;
use App\Livewire\School\AcademicSessions\AcademicSessionEdit;

// Teacher assignments
use App\Livewire\School\TeacherAssignments\TeacherAssignmentIndex;
use App\Livewire\School\TeacherAssignments\TeacherAssignmentCreate;
use App\Livewire\School\TeacherAssignments\TeacherAssignmentEdit;

// Terms
use App\Livewire\School\Terms\TermIndex;
use App\Livewire\School\Terms\TermCreate;
use App\Livewire\School\Terms\TermEdit;

// Subjects and school settings
use App\Livewire\School\Subjects\SubjectIndex;
use App\Livewire\School\Subjects\SubjectCreate;
use App\Livewire\School\Subjects\SubjectEdit;
use App\Livewire\School\FeeStructures\FeeStructureIndex;
use App\Livewire\School\Settings\BankDetails;
use App\Livewire\School\ClassHeadTeachers\ClassHeadTeacherIndex;
use App\Livewire\School\Exams\ResultsApprovalIndex;
use App\Livewire\School\Promotions\PromotionReviewIndex;
use App\Livewire\School\Promotions\PromotionExecutionIndex;
use App\Livewire\School\Principals\PrincipalIndex;
use App\Livewire\School\Receptionists\ReceptionistIndex;
use App\Livewire\School\Subscription\Plans;



// Treasury
use App\Livewire\School\Treasuries\TreasuryIndex;
use App\Livewire\Treasury\Dashboard as TreasuryDashboard;
use App\Livewire\Treasury\PaymentVerification;
use App\Livewire\Treasury\ReceiptManagement;
use App\Livewire\Treasury\PaymentReconciliation;
use App\Livewire\Treasury\FinancialReports;
use App\Livewire\Treasury\OutstandingBalances;
use App\Livewire\Treasury\FeeAssignment;
use App\Livewire\Treasury\InvoiceManagement;

// Timetable
use App\Livewire\School\Timetables\TimetableIndex;

// Parent portal
use App\Livewire\Parent\Dashboard as ParentDashboard;
use App\Livewire\Parent\Children\Index as ParentChildrenIndex;
use App\Livewire\Parent\Children\StudentProfile;
use App\Livewire\Parent\Attendance\AttendanceIndex as ParentAttendanceIndex;
use App\Livewire\Parent\Results\ResultsIndex;
use App\Livewire\Parent\Fees\FeesIndex;
use App\Livewire\Parent\Payments\PaymentHistory;
use App\Livewire\Parent\Payments\ReceiptHistory;
use App\Livewire\Parent\Payments\InvoiceDetails;
use App\Livewire\Parent\Payments\ReceiptDetails;
use App\Livewire\Parent\Announcements\AnnouncementIndex;
use App\Livewire\Parent\Timetable\TimetableIndex as ParentTimetableIndex;
use App\Livewire\Parent\ParentProfile;

use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\AttendanceIndex as StudentAttendanceIndex;
use App\Livewire\Student\ExamResults;
use App\Livewire\Student\ClassTimetable;
use App\Livewire\Student\ClassTeachers;
use App\Livewire\Student\Invoices;
use App\Livewire\Student\InvoiceDetails as StudentInvoiceDetails;
use App\Livewire\Student\PaymentHistory as StudentPaymentHistory;
use App\Livewire\Student\Profile;


use App\Livewire\Teacher\Dashboard as TeacherDashboard;
use App\Livewire\Teacher\MyClasses;
use App\Livewire\Teacher\MyTimetable;
use App\Livewire\Teacher\TakeAttendance;
use App\Livewire\SchoolAdmin\AttendanceSubmissions;
use App\Livewire\Teacher\EnterScores;
use App\Livewire\Teacher\Promotions\PromotionRecommendations;

use App\Livewire\Receptionist\Dashboard as ReceptionistDashboard;
use App\Http\Controllers\School\PaystackSubscriptionController;
use App\Livewire\School\Subscription\PaymentHistory as SchoolPayment;

Route::get(
    '/school/subscription/paystack/callback',
    [PaystackSubscriptionController::class, 'callback']
)->name('school.subscription.paystack.callback');

Route::post(
    '/paystack/webhook',
    [PaystackSubscriptionController::class, 'webhook']
)->name('paystack.webhook');

Route::middleware(['auth', 'school.tenant', 'role:Receptionist'])
    ->prefix('receptionist')
    ->name('receptionist.')
    ->group(function () {

        Route::get('/dashboard', ReceptionistDashboard::class)
            ->name('dashboard');
             Route::get('/enrollments', EnrollmentIndex::class)
            ->middleware('permission:view enrollments')
            ->name('enrollments.index');

        Route::get('/enrollments/create', EnrollmentCreate::class)
            ->middleware('permission:manage admissions')
            ->name('enrollments.create');

        Route::get('/enrollments/{enrollmentId}/edit', EnrollmentEdit::class)
            ->whereNumber('enrollmentId')
            ->middleware('permission:manage students')
            ->name('enrollments.edit');
            Route::get('/parents', ParentIndex::class)
    ->middleware('permission:view parents')
    ->name('parents.index');

Route::get('/parents/create', ParentCreate::class)
    ->middleware('permission:create parents')
    ->name('parents.create');

Route::get('/parents/{parentId}/edit', ParentEdit::class)
    ->whereNumber('parentId')
    ->middleware('permission:edit parents')
    ->name('parents.edit');


     Route::get('/students', StudentIndex::class)
            ->middleware('permission:view students')
            ->name('students.index');

        Route::get('/students/create', StudentCreate::class)
            ->middleware('permission:view students')
            ->name('students.create');

        Route::get('/students/{student}/edit', StudentEdit::class)
            ->whereNumber('student')
            ->middleware('permission:view students')
            ->name('students.edit');

        Route::get('/students/{student}/show', StudentShow::class)
            ->whereNumber('student')
            ->middleware('permission:view students')
            ->name('students.show');

});

Route::middleware(['auth', 'school.tenant', 'role:Teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', TeacherDashboard::class)
            ->name('dashboard');
            Route::get('/promotions/recommendations', PromotionRecommendations::class)
                ->name('promotions.recommendations');

             Route::get('/classes', MyClasses::class)
            ->name('classes');
            Route::get('/timetable', MyTimetable::class)
    ->name('timetable');

     Route::get('/attendance', TakeAttendance::class)
            ->name('attendance');

            Route::get('/enter-scores', EnterScores::class)
    ->name('enter-scores');
    });


    Route::middleware(['auth', 'role:School Admin'])
    ->prefix('school-admin')
    ->name('school-admin.')
    ->group(function () {
        Route::get('/attendance-submissions', AttendanceSubmissions::class)
            ->name('attendance-submissions');
    });



Route::middleware(['auth','school.tenant', 'role:Student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', StudentDashboard::class)
            ->name('dashboard');
        Route::get('/attendance', StudentAttendanceIndex::class)
            ->name('attendance');
            Route::get('/exam-results', ExamResults::class)
    ->name('exam-results');

    Route::get('/class-timetable', ClassTimetable::class)
    ->name('class-timetable');
    Route::get('/class-teachers', ClassTeachers::class)
    ->name('class-teachers');
    Route::get('/invoices', Invoices::class)
    ->name('invoices.index');

Route::get('/invoices/{invoice}', StudentInvoiceDetails::class)
    ->whereNumber('invoice')
    ->name('invoices.show');
    Route::get('/payment-history', StudentPaymentHistory::class)
    ->name('payment-history');

    Route::get('/profile', Profile::class)
    ->name('profile');

    });


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

Route::get('/login', [LoginController::class, 'create'])
    ->middleware('guest')
    ->name('login');


/*
|--------------------------------------------------------------------------
| Parent Portal
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'school.tenant', 'role:Parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', ParentDashboard::class)
            ->name('dashboard');

        // Children
        Route::get('/children', ParentChildrenIndex::class)
            ->name('children.index');

        Route::get('/children/{studentId}', StudentProfile::class)
            ->whereNumber('studentId')
            ->name('children.show');

        // Attendance
        Route::get('/attendance', ParentAttendanceIndex::class)
            ->name('attendance.index');

        // Results
        Route::get('/results', ResultsIndex::class)
            ->name('results.index');

        // Fees
        Route::get('/fees', FeesIndex::class)
            ->name('fees.index');

        // Invoice details
        Route::get('/fees/{invoice}', InvoiceDetails::class)
            ->whereNumber('invoice')
            ->name('fees.show');

        // Payment history
        Route::get('/payments', PaymentHistory::class)
            ->name('payments.index');

        // Receipt history
        Route::get('/receipts', ReceiptHistory::class)
            ->name('receipts.index');

        Route::get('/receipts/{receipt}', ReceiptDetails::class)
    ->whereNumber('receipt')
    ->name('receipts.show');

    Route::get('/announcements', AnnouncementIndex::class)
    ->name('announcements.index');
    Route::get('/timetable', ParentTimetableIndex::class)
    ->name('timetable.index');
    Route::get('/profile', ParentProfile::class)
    ->name('profile');
    });


/*
|--------------------------------------------------------------------------
| Treasury Portal
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'school.tenant', 'role:Treasury'])
    ->prefix('treasury')
    ->name('treasury.')
    ->group(function () {

        Route::get('/dashboard', TreasuryDashboard::class)
            ->name('dashboard');

        Route::get('/payments', PaymentVerification::class)
            ->name('payments');

        Route::get('/receipts', ReceiptManagement::class)
            ->name('receipts');

        Route::get('/reconciliation', PaymentReconciliation::class)
            ->name('reconciliation');

        Route::get('/reports', FinancialReports::class)
            ->name('reports');

        Route::get('/outstanding-balances', OutstandingBalances::class)
            ->name('outstanding-balances');

        Route::get('/fee-assignment', FeeAssignment::class)
            ->name('fee-assignment');

        Route::get('/invoices', InvoiceManagement::class)
            ->name('invoices');
    });


/*
|--------------------------------------------------------------------------
| Platform Super Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Super Admin'])
    ->prefix('platform')
    ->name('platform.')
    ->group(function () {

        Route::view('/dashboard', 'platform.dashboard')
            ->name('dashboard');

        Route::view('/schools', 'platform.schools.index')
            ->name('schools.index');

        Route::view('/schools/create', 'platform.schools.create')
            ->name('schools.create');

        Route::get('/schools/{school}/edit', function (School $school) {
            return view('platform.schools.edit', compact('school'));
        })->name('schools.edit');

        Route::view('/plans', 'platform.plans.index')
            ->name('plans.index');

        Route::view('/subscriptions', 'platform.subscriptions.index')
            ->name('subscriptions.index');

        Route::view('/users', 'platform.users.index')
            ->middleware('permission:view users')
            ->name('users.index');

        Route::view('/activity', 'platform.activity.index')
            ->name('activity.index');

        Route::view('/settings', 'platform.settings.index')
            ->middleware('permission:manage platform settings')
            ->name('settings.index');
    });


/*
|--------------------------------------------------------------------------
| School Portal
|--------------------------------------------------------------------------
*/




// Keep your existing Livewire component imports above this section.

Route::middleware(['auth', 'school.tenant'])
    ->prefix('school')
    ->name('school.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Subscription Management
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:School Admin|Principal')->group(function () {
            Route::get('/subscription/plans', Plans::class)
                ->name('subscription.plans');

            Route::get('/subscription/payment-history', SchoolPayment::class)
                ->name('subscription.payment-history');
        });


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::view('/dashboard', 'school.dashboard')
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Principals and Receptionists
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:School Admin|Principal')->group(function () {

            Route::get('/principals', PrincipalIndex::class)
                ->name('principals.index');

            Route::get('/receptionists', ReceptionistIndex::class)
                ->name('receptionists.index');

        });


        /*
        |--------------------------------------------------------------------------
        | Exams and Student Promotions
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:School Admin|Principal')->group(function () {

            Route::get('/exams/results-approval', ResultsApprovalIndex::class)
                ->name('exams.results-approval.index');

            Route::get('/promotions/review', PromotionReviewIndex::class)
                ->name('promotions.review.index');

            Route::get('/promotions/execution', PromotionExecutionIndex::class)
                ->name('promotions.execution.index');

        });


        /*
        |--------------------------------------------------------------------------
        | School Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/settings/bank-details', BankDetails::class)
            ->middleware('permission:manage school settings')
            ->name('settings.bank-details');

        Route::get('/class-head-teachers', ClassHeadTeacherIndex::class)
            ->middleware('permission:manage teachers')
            ->name('class-head-teachers.index');


        /*
        |--------------------------------------------------------------------------
        | Timetable Management
        |--------------------------------------------------------------------------
        */

        Route::get('/timetable', TimetableIndex::class)
            ->middleware('role:School Admin|Principal')
            ->name('timetable.index');


        /*
        |--------------------------------------------------------------------------
        | Treasury and Fee Structures
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:School Admin|Principal')->group(function () {

            Route::get('/treasuries', TreasuryIndex::class)
                ->name('treasuries.index');

            Route::get('/fee-structures', FeeStructureIndex::class)
                ->name('fee-structures.index');

        });


        /*
        |--------------------------------------------------------------------------
        | Enrollments
        |--------------------------------------------------------------------------
        */

        Route::get('/enrollments', EnrollmentIndex::class)
            ->middleware('permission:manage admissions')
            ->name('enrollments.index');

        Route::get('/enrollments/create', EnrollmentCreate::class)
            ->middleware('permission:manage admissions')
            ->name('enrollments.create');

        Route::get('/enrollments/{enrollmentId}/edit', EnrollmentEdit::class)
            ->whereNumber('enrollmentId')
            ->middleware('permission:manage admissions')
            ->name('enrollments.edit');


        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance', AttendanceIndex::class)
            ->middleware('permission:view attendance')
            ->name('attendance.index');


        /*
        |--------------------------------------------------------------------------
        | Exams
        |--------------------------------------------------------------------------
        */

        Route::get('/exams', ExamIndex::class)
            ->middleware('permission:view exams')
            ->name('exams.index');


        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        Route::get('/payments', PaymentIndex::class)
            ->middleware('permission:record payments')
            ->name('payments.index');


        /*
        |--------------------------------------------------------------------------
        | Parents
        |--------------------------------------------------------------------------
        */

        Route::get('/parents', ParentIndex::class)
            ->middleware('permission:view parents')
            ->name('parents.index');

        Route::get('/parents/create', ParentCreate::class)
            ->middleware('permission:manage parents')
            ->name('parents.create');

        Route::get('/parents/{parentId}/edit', ParentEdit::class)
            ->whereNumber('parentId')
            ->middleware('permission:manage parents')
            ->name('parents.edit');


        /*
        |--------------------------------------------------------------------------
        | Teacher Assignments
        |--------------------------------------------------------------------------
        */

        Route::get('/teacher-assignments', TeacherAssignmentIndex::class)
            ->middleware('permission:manage teachers')
            ->name('teacher-assignments.index');

        Route::get('/teacher-assignments/create', TeacherAssignmentCreate::class)
            ->middleware('permission:manage teachers')
            ->name('teacher-assignments.create');

        Route::get(
            '/teacher-assignments/{assignmentId}/edit',
            TeacherAssignmentEdit::class
        )
            ->whereNumber('assignmentId')
            ->middleware('permission:manage teachers')
            ->name('teacher-assignments.edit');


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        Route::get('/classes', ClassIndex::class)
            ->middleware('permission:manage classes')
            ->name('classes.index');

        Route::get('/classes/create', ClassCreate::class)
            ->middleware('permission:manage classes')
            ->name('classes.create');

        Route::get('/classes/{classId}/edit', ClassEdit::class)
            ->whereNumber('classId')
            ->middleware('permission:manage classes')
            ->name('classes.edit');


        /*
        |--------------------------------------------------------------------------
        | Subjects
        |--------------------------------------------------------------------------
        */

        Route::get('/subjects', SubjectIndex::class)
            ->middleware('permission:manage subjects')
            ->name('subjects.index');

        Route::get('/subjects/create', SubjectCreate::class)
            ->middleware('permission:manage subjects')
            ->name('subjects.create');

        Route::get('/subjects/{subjectId}/edit', SubjectEdit::class)
            ->whereNumber('subjectId')
            ->middleware('permission:manage subjects')
            ->name('subjects.edit');


        /*
        |--------------------------------------------------------------------------
        | Terms
        |--------------------------------------------------------------------------
        */

        Route::get('/terms', TermIndex::class)
            ->middleware('permission:manage academic sessions')
            ->name('terms.index');

        Route::get('/terms/create', TermCreate::class)
            ->middleware('permission:manage academic sessions')
            ->name('terms.create');

        Route::get('/terms/{termId}/edit', TermEdit::class)
            ->whereNumber('termId')
            ->middleware('permission:manage academic sessions')
            ->name('terms.edit');


        /*
        |--------------------------------------------------------------------------
        | Academic Sessions
        |--------------------------------------------------------------------------
        */

        Route::get('/academic-sessions', AcademicSessionIndex::class)
            ->middleware('permission:manage academic sessions')
            ->name('academic-sessions.index');

        Route::get('/academic-sessions/create', AcademicSessionCreate::class)
            ->middleware('permission:manage academic sessions')
            ->name('academic-sessions.create');

        Route::get(
            '/academic-sessions/{sessionId}/edit',
            AcademicSessionEdit::class
        )
            ->whereNumber('sessionId')
            ->middleware('permission:manage academic sessions')
            ->name('academic-sessions.edit');


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        Route::get('/sections', SectionIndex::class)
            ->middleware('permission:manage classes')
            ->name('sections.index');

        Route::get('/sections/create', SectionCreate::class)
            ->middleware('permission:manage classes')
            ->name('sections.create');

        Route::get('/sections/{sectionId}/edit', SectionEdit::class)
            ->whereNumber('sectionId')
            ->middleware('permission:manage classes')
            ->name('sections.edit');


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        Route::get('/students', StudentIndex::class)
            ->middleware('permission:view students')
            ->name('students.index');

        Route::get('/students/create', StudentCreate::class)
            ->middleware('permission:manage students')
            ->name('students.create');

        Route::get('/students/{student}/edit', StudentEdit::class)
            ->whereNumber('student')
            ->middleware('permission:manage students')
            ->name('students.edit');

        Route::get('/students/{student}/show', StudentShow::class)
            ->whereNumber('student')
            ->middleware('permission:view students')
            ->name('students.show');


        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        */

        Route::prefix('teachers')
            ->name('teachers.')
            ->group(function () {

                Route::get('/', TeacherIndex::class)
                    ->middleware('permission:view teachers')
                    ->name('index');

                Route::get('/create', TeacherCreate::class)
                    ->middleware('permission:manage teachers')
                    ->name('create');

                Route::get('/{teacher}/edit', TeacherEdit::class)
                    ->whereNumber('teacher')
                    ->middleware('permission:manage teachers')
                    ->name('edit');

                Route::get('/{teacher}', TeacherShow::class)
                    ->whereNumber('teacher')
                    ->middleware('permission:view teachers')
                    ->name('show');

            });

    });


/*
|--------------------------------------------------------------------------
| General Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

});

require __DIR__ . '/settings.php';

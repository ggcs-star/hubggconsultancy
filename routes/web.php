<?php

use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseLessonController;
use App\Http\Controllers\Admin\CourseModuleController;
use App\Http\Controllers\Admin\CourseModuleQuizController;
use App\Http\Controllers\Admin\CourseQuizCheckpointController;
use App\Http\Controllers\Admin\CourseQuizQuestionController;
use App\Http\Controllers\Admin\CourseQuizReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OnboardingAssessmentAnswerController as AdminOnboardingAssessmentAnswerController;
use App\Http\Controllers\Admin\OnboardingAssessmentController as AdminOnboardingAssessmentController;
use App\Http\Controllers\Admin\OnboardingAssessmentQuestionController;
use App\Http\Controllers\Admin\PlaceholderController as AdminPlaceholderController;
use App\Http\Controllers\Admin\SaasProductController;
use App\Http\Controllers\Admin\SalespersonApplicationController as AdminSalespersonApplicationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\CertificateController as UserCertificateController;
use App\Http\Controllers\User\CourseController as UserCourseController;
use App\Http\Controllers\User\CourseLessonProgressController;
use App\Http\Controllers\User\CourseQuizAnswerController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\OnboardingAssessmentController as UserOnboardingAssessmentController;
use App\Http\Controllers\User\PlaceholderController as UserPlaceholderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Admin\SupportIssueTypeController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\User\SupportTicketController as UserSupportTicketController;
use App\Http\Controllers\Admin\SalesManualController;
use App\Http\Controllers\User\SalesManualController as UserSalesManualController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/google', [AuthController::class, 'googleStub'])->name('auth.google');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/clients', [ClientController::class, 'index'])->name('clients');
      
     Route::prefix('support')->name('support.')->group(function () {

    // =========================
    // SUPPORT TICKETS
    // =========================

    Route::get('/tickets', [SupportTicketController::class, 'index'])
        ->name('tickets.index');

    Route::get('/tickets/{ticket}', [SupportTicketController::class, 'show'])
        ->name('tickets.show');

    Route::post('/tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])
        ->name('tickets.reply');

    Route::patch('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])
        ->name('tickets.status');


    // =========================
    // ISSUE TYPES
    // =========================

    Route::resource('issue-types', SupportIssueTypeController::class)
        ->except(['show']);

    Route::patch(
        '/issue-types/{issueType}/toggle-status',
        [SupportIssueTypeController::class, 'toggleStatus']
    )->name('issue-types.toggle-status');

});
        Route::get('/salesperson-applications', [AdminSalespersonApplicationController::class, 'index'])->name('salesperson-applications');
        Route::post('/salesperson-applications/{user}/approve', [AdminSalespersonApplicationController::class, 'approve'])->name('salesperson-applications.approve');
        Route::post('/salesperson-applications/{user}/reject', [AdminSalespersonApplicationController::class, 'reject'])->name('salesperson-applications.reject');
        Route::put('/salesperson-applications/{user}/courses', [AdminSalespersonApplicationController::class, 'updateCourses'])->name('salesperson-applications.courses.update');

        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [AdminCourseController::class, 'index'])->name('index');
            Route::post('/', [AdminCourseController::class, 'store'])->name('store');
            Route::get('/{course}', [AdminCourseController::class, 'show'])->name('show');
            Route::put('/{course}', [AdminCourseController::class, 'update'])->name('update');
            Route::delete('/{course}', [AdminCourseController::class, 'destroy'])->name('destroy');
            Route::patch('/{course}/publish-toggle', [AdminCourseController::class, 'togglePublish'])->name('publish.toggle');
            Route::get('/{course}/preview', [AdminCourseController::class, 'preview'])->name('preview');
            Route::put('/{course}/certificate', [AdminCourseController::class, 'updateCertificate'])->name('certificate.update');
        });

        Route::post('/courses/{course}/modules', [CourseModuleController::class, 'store'])->name('course-modules.store');
        Route::post('/courses/{course}/modules/reorder', [CourseModuleController::class, 'reorder'])->name('course-modules.reorder');
        Route::put('/course-modules/{module}', [CourseModuleController::class, 'update'])->name('course-modules.update');
        Route::delete('/course-modules/{module}', [CourseModuleController::class, 'destroy'])->name('course-modules.destroy');
        Route::post('/course-modules/{module}/items/reorder', [CourseModuleController::class, 'reorderItems'])->name('course-modules.items.reorder');

        Route::get('/course-modules/{courseModule}/lessons/create', [CourseLessonController::class, 'create'])->name('course-lessons.create');
        Route::post('/course-modules/{courseModule}/lessons', [CourseLessonController::class, 'store'])->name('course-lessons.store');
        Route::get('/course-lessons/{lesson}/edit', [CourseLessonController::class, 'edit'])->name('course-lessons.edit');
        Route::put('/course-lessons/{lesson}', [CourseLessonController::class, 'update'])->name('course-lessons.update');
        Route::delete('/course-lessons/{lesson}', [CourseLessonController::class, 'destroy'])->name('course-lessons.destroy');

        Route::post('/course-lessons/{lesson}/checkpoints', [CourseQuizCheckpointController::class, 'store'])->name('course-quiz-checkpoints.store');
        Route::put('/course-quiz-checkpoints/{checkpoint}', [CourseQuizCheckpointController::class, 'update'])->name('course-quiz-checkpoints.update');
        Route::delete('/course-quiz-checkpoints/{checkpoint}', [CourseQuizCheckpointController::class, 'destroy'])->name('course-quiz-checkpoints.destroy');

        Route::post('/course-modules/{module}/quizzes', [CourseModuleQuizController::class, 'store'])->name('course-module-quizzes.store');
        Route::get('/course-module-quizzes/{quiz}/edit', [CourseModuleQuizController::class, 'edit'])->name('course-module-quizzes.edit');
        Route::put('/course-module-quizzes/{quiz}', [CourseModuleQuizController::class, 'update'])->name('course-module-quizzes.update');
        Route::delete('/course-module-quizzes/{quiz}', [CourseModuleQuizController::class, 'destroy'])->name('course-module-quizzes.destroy');

        Route::post('/course-quiz-checkpoints/{checkpoint}/questions', [CourseQuizQuestionController::class, 'store'])->name('course-quiz-questions.store');
        Route::put('/course-quiz-questions/{question}', [CourseQuizQuestionController::class, 'update'])->name('course-quiz-questions.update');
        Route::delete('/course-quiz-questions/{question}', [CourseQuizQuestionController::class, 'destroy'])->name('course-quiz-questions.destroy');

        Route::get('/course-quiz-answers/pending', [CourseQuizReviewController::class, 'index'])->name('course-quiz-answers.pending');
        Route::patch('/course-quiz-answers/{answer}/grade', [CourseQuizReviewController::class, 'grade'])->name('course-quiz-answers.grade');

      Route::get('/certificates', [AdminCertificateController::class, 'index'])
    ->name('certificates');


/*
|--------------------------------------------------------------------------
| Sales Manuals
|--------------------------------------------------------------------------
*/

Route::prefix('sales-manuals')
    ->name('manuals.')
    ->group(function () {

        Route::get('/', [SalesManualController::class, 'index'])
            ->name('index');

        Route::get('/create', [SalesManualController::class, 'create'])
            ->name('create');

        Route::post('/', [SalesManualController::class, 'store'])
            ->name('store');

        Route::get('/{manual}/edit', [SalesManualController::class, 'edit'])
            ->name('edit');

        Route::get('/{manual}', [SalesManualController::class, 'show'])
            ->name('show');

        Route::put('/{manual}', [SalesManualController::class, 'update'])
            ->name('update');

        Route::delete('/{manual}', [SalesManualController::class, 'destroy'])
            ->name('destroy');

        Route::patch('/{manual}/publish', [SalesManualController::class, 'togglePublish'])
            ->name('publish');

        Route::patch('/{manual}/active', [SalesManualController::class, 'toggleActive'])
            ->name('active');

        Route::patch('/{manual}/featured', [SalesManualController::class, 'toggleFeatured'])
            ->name('featured');

        Route::patch('/{manual}/pinned', [SalesManualController::class, 'togglePinned'])
            ->name('pinned');

        Route::delete('/attachments/{attachment}', [SalesManualController::class, 'deleteAttachment'])
            ->name('attachments.delete');
    });


Route::get('/social-guide', [AdminPlaceholderController::class, 'socialGuide'])
    ->name('social-guide');

Route::get('/settings', [AdminPlaceholderController::class, 'settings'])
    ->name('settings');

        Route::prefix('saas-products')->name('saas-products.')->group(function () {
            Route::get('/', [SaasProductController::class, 'index'])->name('index');
            Route::post('/', [SaasProductController::class, 'store'])->name('store');
            Route::put('/{saasProduct}', [SaasProductController::class, 'update'])->name('update');
            Route::patch('/{saasProduct}/toggle-status', [SaasProductController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{saasProduct}', [SaasProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('onboarding-assessment')->name('onboarding-assessment.')->group(function () {
            Route::get('/', [AdminOnboardingAssessmentController::class, 'index'])->name('index');
            Route::put('/settings', [AdminOnboardingAssessmentController::class, 'updateSettings'])->name('settings.update');
            Route::get('/results/{user}', [AdminOnboardingAssessmentController::class, 'resultShow'])->name('results.show');
            Route::delete('/results/{user}/retake', [AdminOnboardingAssessmentController::class, 'retake'])->name('results.retake');
            Route::post('/questions', [OnboardingAssessmentQuestionController::class, 'store'])->name('questions.store');
            Route::put('/questions/{question}', [OnboardingAssessmentQuestionController::class, 'update'])->name('questions.update');
            Route::delete('/questions/{question}', [OnboardingAssessmentQuestionController::class, 'destroy'])->name('questions.destroy');
            Route::patch('/answers/{answer}/grade', [AdminOnboardingAssessmentAnswerController::class, 'grade'])->name('answers.grade');
        });
    });

Route::middleware(['auth', 'role:user'])
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/onboarding-assessment', [UserOnboardingAssessmentController::class, 'index'])->name('onboarding-assessment.index');
        Route::post('/onboarding-assessment/submit', [UserOnboardingAssessmentController::class, 'submit'])->name('onboarding-assessment.submit');

        Route::get('/training', [UserCourseController::class, 'index'])->name('training');
        Route::get('/courses', [UserCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [UserCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/quiz-checkpoints/{checkpoint}/answers', [CourseQuizAnswerController::class, 'store'])->name('course-quiz-answers.store');
        Route::post('/courses/lessons/{lesson}/progress', [CourseLessonProgressController::class, 'store'])->name('course-lesson-progress.store');

        Route::get('/certificates', [UserCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [UserCertificateController::class, 'show'])->name('certificates.show');

Route::get('/sales-manuals', [UserSalesManualController::class, 'index'])
    ->name('manuals');

Route::get('/sales-manuals/{manual}', [UserSalesManualController::class, 'show'])
    ->name('manuals.show');
            Route::get('/social-guide', [UserPlaceholderController::class, 'socialGuide'])->name('social-guide');
   
   
   Route::prefix('support')->name('support.')->group(function () {

    Route::get('/tickets', [UserSupportTicketController::class, 'index'])
        ->name('tickets.index');

    Route::get('/tickets/create', [UserSupportTicketController::class, 'create'])
        ->name('tickets.create');

    Route::post('/tickets', [UserSupportTicketController::class, 'store'])
        ->name('tickets.store');

    Route::get('/tickets/{ticket}', [UserSupportTicketController::class, 'show'])
        ->name('tickets.show');

    Route::post('/tickets/{ticket}/reply', [UserSupportTicketController::class, 'reply'])
        ->name('tickets.reply');

});
   
   
        });


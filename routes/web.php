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
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\OnboardingChecklistController as AdminOnboardingChecklistController;
use App\Http\Controllers\Admin\OnboardingAssessmentAnswerController as AdminOnboardingAssessmentAnswerController;
use App\Http\Controllers\Admin\OnboardingAssessmentController as AdminOnboardingAssessmentController;
use App\Http\Controllers\Admin\OnboardingAssessmentQuestionController;
use App\Http\Controllers\Admin\OnboardingAssessmentQuizController;
use App\Http\Controllers\Admin\PlaceholderController as AdminPlaceholderController;
use App\Http\Controllers\Admin\ResourceCheckpointController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\ResourceQuizQuestionController;
use App\Http\Controllers\Admin\SaasProductController;
use App\Http\Controllers\User\SaasProductController as UserSaasProductController;
use App\Http\Controllers\Admin\SalespersonApplicationController as AdminSalespersonApplicationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseLessonVideoController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\User\CertificateController as UserCertificateController;
use App\Http\Controllers\User\CourseController as UserCourseController;
use App\Http\Controllers\User\CourseLessonProgressController;
use App\Http\Controllers\User\CourseQuizAnswerController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\DocumentController as UserDocumentController;
use App\Http\Controllers\User\OnboardingChecklistController as UserOnboardingChecklistController;
use App\Http\Controllers\User\OnboardingAssessmentController as UserOnboardingAssessmentController;
use App\Http\Controllers\User\PlaceholderController as UserPlaceholderController;
use App\Http\Controllers\User\PointsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ResourceController as UserResourceController;
use App\Http\Controllers\User\ResourceQuizAnswerController;
use App\Http\Controllers\Admin\SupportIssueTypeController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\User\SupportTicketController as UserSupportTicketController;
use App\Http\Controllers\Admin\SalesManualController;
use App\Http\Controllers\User\SalesManualController as UserSalesManualController;
use App\Http\Controllers\Admin\SalesToolkitController;
use App\Http\Controllers\User\SalesToolkitController as UserSalesToolkitController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\User\FaqController as UserFaqController;
use App\Http\Controllers\Admin\ScriptTopicController;
use App\Http\Controllers\Admin\ScriptItemController;
use App\Http\Controllers\User\ScriptController as UserScriptController;
use App\Http\Controllers\Admin\CertificateTemplateController;
use App\Http\Controllers\Admin\ContestController as AdminContestController;
use App\Http\Controllers\User\ContestController as UserContestController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\User\TeamController as UserTeamController;
use App\Http\Controllers\Admin\ContestTrackerController as AdminContestTrackerController;
use App\Http\Controllers\User\ContestTrackerController as UserContestTrackerController;
use App\Http\Controllers\Admin\LeaderboardController as AdminLeaderboardController;
use App\Http\Controllers\User\LeaderboardController as UserLeaderboardController;
use App\Http\Controllers\Admin\IncentiveController as AdminIncentiveController;
use App\Http\Controllers\User\IncentiveController as UserIncentiveController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\User\LeadController as UserLeadController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\LearningProgressController as AdminLearningProgressController;
use App\Http\Controllers\User\LearningProgressController as UserLearningProgressController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\User\AnnouncementController as UserAnnouncementController;
use App\Http\Controllers\Admin\AchieverController as AdminAchieverController;
use App\Http\Controllers\User\AchieverController as UserAchieverController;
use App\Http\Controllers\User\PerformanceController as UserPerformanceController;
use App\Http\Controllers\Admin\HallOfFameController as AdminHallOfFameController;
use App\Http\Controllers\User\HallOfFameController as UserHallOfFameController;
use App\Http\Controllers\Admin\SuccessStoryController as AdminSuccessStoryController;
use App\Http\Controllers\User\SuccessStoryController as UserSuccessStoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::patch('/clients/{client}/status', [ClientController::class, 'updateStatus'])->name('clients.status.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
      
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
| Certificate Templates
|--------------------------------------------------------------------------
*/

Route::get(
    '/certificate-templates',
    [
        CertificateTemplateController::class,
        'index',
    ]
)->name('certificates.templates.index');


Route::get(
    '/courses/{course}/certificate-template',
    [
        CertificateTemplateController::class,
        'assign',
    ]
)->name('certificates.templates.assign');


Route::put(
    '/courses/{course}/certificate-template',
    [
        CertificateTemplateController::class,
        'updateAssignment',
    ]
)->name('certificates.templates.update');


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
            Route::delete('/results/{user}/quizzes/{quiz}/retake', [AdminOnboardingAssessmentController::class, 'retakeQuiz'])->name('results.retake-quiz');
            Route::post('/quizzes', [OnboardingAssessmentQuizController::class, 'store'])->name('quizzes.store');
            Route::post('/quizzes/reorder', [OnboardingAssessmentQuizController::class, 'reorder'])->name('quizzes.reorder');
            Route::put('/quizzes/{quiz}', [OnboardingAssessmentQuizController::class, 'update'])->name('quizzes.update');
            Route::patch('/quizzes/{quiz}/toggle-published', [OnboardingAssessmentQuizController::class, 'togglePublished'])->name('quizzes.toggle-published');
            Route::delete('/quizzes/{quiz}', [OnboardingAssessmentQuizController::class, 'destroy'])->name('quizzes.destroy');
            Route::post('/quizzes/{quiz}/questions', [OnboardingAssessmentQuestionController::class, 'store'])->name('questions.store');
            Route::put('/questions/{question}', [OnboardingAssessmentQuestionController::class, 'update'])->name('questions.update');
            Route::delete('/questions/{question}', [OnboardingAssessmentQuestionController::class, 'destroy'])->name('questions.destroy');
            Route::patch('/answers/{answer}/grade', [AdminOnboardingAssessmentAnswerController::class, 'grade'])->name('answers.grade');
        });

        Route::prefix('resources')->name('resources.')->group(function () {
            Route::get('/', [AdminResourceController::class, 'index'])->name('index');
            Route::post('/', [AdminResourceController::class, 'store'])->name('store');
            Route::get('/{resource}', [AdminResourceController::class, 'show'])->name('show');
            Route::put('/{resource}', [AdminResourceController::class, 'update'])->name('update');
            Route::delete('/{resource}', [AdminResourceController::class, 'destroy'])->name('destroy');
            Route::patch('/{resource}/publish-toggle', [AdminResourceController::class, 'togglePublish'])->name('publish.toggle');
        });

        Route::post('/resources/{resource}/checkpoints', [ResourceCheckpointController::class, 'store'])->name('resource-checkpoints.store');
        Route::put('/resource-checkpoints/{checkpoint}', [ResourceCheckpointController::class, 'update'])->name('resource-checkpoints.update');
        Route::delete('/resource-checkpoints/{checkpoint}', [ResourceCheckpointController::class, 'destroy'])->name('resource-checkpoints.destroy');

        Route::post('/resource-checkpoints/{checkpoint}/questions', [ResourceQuizQuestionController::class, 'store'])->name('resource-quiz-questions.store');
        Route::put('/resource-quiz-questions/{question}', [ResourceQuizQuestionController::class, 'update'])->name('resource-quiz-questions.update');
        Route::delete('/resource-quiz-questions/{question}', [ResourceQuizQuestionController::class, 'destroy'])->name('resource-quiz-questions.destroy');

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
            Route::post('/', [AdminDocumentController::class, 'store'])->name('store');
            Route::put('/{document}', [AdminDocumentController::class, 'update'])->name('update');
            Route::delete('/{document}', [AdminDocumentController::class, 'destroy'])->name('destroy');
            Route::patch('/{document}/publish-toggle', [AdminDocumentController::class, 'togglePublish'])->name('publish.toggle');
        });

        Route::prefix('onboarding-checklist')->name('onboarding-checklist.')->group(function () {
            Route::get('/', [AdminOnboardingChecklistController::class, 'index'])->name('index');
            Route::post('/', [AdminOnboardingChecklistController::class, 'store'])->name('store');
            Route::put('/{onboardingChecklistItem}', [AdminOnboardingChecklistController::class, 'update'])->name('update');
            Route::delete('/{onboardingChecklistItem}', [AdminOnboardingChecklistController::class, 'destroy'])->name('destroy');
            Route::patch('/{onboardingChecklistItem}/publish-toggle', [AdminOnboardingChecklistController::class, 'togglePublish'])->name('publish.toggle');
            Route::get('/progress', [AdminOnboardingChecklistController::class, 'progress'])->name('progress');
        });

        Route::prefix('sales-toolkit')->name('sales-toolkit.')->group(function () {
            Route::get('/', [SalesToolkitController::class, 'index'])->name('index');
            Route::post('/', [SalesToolkitController::class, 'store'])->name('store');
            Route::put('/{salesToolkitItem}', [SalesToolkitController::class, 'update'])->name('update');
            Route::delete('/{salesToolkitItem}', [SalesToolkitController::class, 'destroy'])->name('destroy');
            Route::patch('/{salesToolkitItem}/publish-toggle', [SalesToolkitController::class, 'togglePublish'])->name('publish.toggle');
        });

        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [AdminEventController::class, 'index'])->name('index');
            Route::post('/', [AdminEventController::class, 'store'])->name('store');
            Route::put('/{event}', [AdminEventController::class, 'update'])->name('update');
            Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy');
            Route::patch('/{event}/publish-toggle', [AdminEventController::class, 'togglePublish'])->name('publish.toggle');
            Route::get('/{event}/registrants', [AdminEventController::class, 'registrants'])->name('registrants');
        });

        Route::prefix('contests')->name('contests.')->group(function () {
            Route::get('/', [AdminContestController::class, 'index'])->name('index');
            Route::get('/create', [AdminContestController::class, 'create'])->name('create');
            Route::post('/', [AdminContestController::class, 'store'])->name('store');
            Route::get('/{contest}/edit', [AdminContestController::class, 'edit'])->name('edit');
            Route::put('/{contest}', [AdminContestController::class, 'update'])->name('update');
            Route::delete('/{contest}', [AdminContestController::class, 'destroy'])->name('destroy');
            Route::patch('/{contest}/active-toggle', [AdminContestController::class, 'toggleActive'])->name('active.toggle');
            Route::get('/{contest}/participants', [AdminContestController::class, 'participants'])->name('participants');
            Route::post('/{contest}/achievements', [AdminContestController::class, 'storeAchievement'])->name('achievements.store');
        });
        Route::delete('/contest-achievements/{achievement}', [AdminContestController::class, 'destroyAchievement'])->name('contest-achievements.destroy');
        Route::post('/contest-target-types', [AdminContestController::class, 'storeTargetType'])->name('contest-target-types.store');

        Route::get('/contest-tracker', [AdminContestTrackerController::class, 'index'])->name('contest-tracker.index');
        Route::get('/leaderboard', [AdminLeaderboardController::class, 'index'])->name('leaderboard.index');

        Route::prefix('incentives')->name('incentives.')->group(function () {
            Route::get('/', [AdminIncentiveController::class, 'index'])->name('index');
            Route::post('/', [AdminIncentiveController::class, 'store'])->name('store');
            Route::delete('/{incentive}', [AdminIncentiveController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('teams')->name('teams.')->group(function () {
            Route::get('/', [AdminTeamController::class, 'index'])->name('index');
            Route::get('/{user}', [AdminTeamController::class, 'show'])->name('show');
            Route::post('/{user}/earnings', [AdminTeamController::class, 'storeEarning'])->name('earnings.store');
        });
        Route::delete('/referral-earnings/{earning}', [AdminTeamController::class, 'destroyEarning'])->name('referral-earnings.destroy');

        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [AdminLeadController::class, 'index'])->name('index');
            Route::get('/create', [AdminLeadController::class, 'create'])->name('create');
            Route::post('/', [AdminLeadController::class, 'store'])->name('store');
            Route::post('/bulk-assign', [AdminLeadController::class, 'bulkAssign'])->name('bulk-assign');
            Route::post('/auto-assign', [AdminLeadController::class, 'autoAssign'])->name('auto-assign');
            Route::get('/{lead}', [AdminLeadController::class, 'show'])->name('show');
            Route::get('/{lead}/edit', [AdminLeadController::class, 'edit'])->name('edit');
            Route::put('/{lead}', [AdminLeadController::class, 'update'])->name('update');
            Route::delete('/{lead}', [AdminLeadController::class, 'destroy'])->name('destroy');
            Route::post('/{lead}/notes', [AdminLeadController::class, 'storeNote'])->name('notes.store');
        });

        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', [AdminCampaignController::class, 'index'])->name('index');
            Route::post('/', [AdminCampaignController::class, 'store'])->name('store');
            Route::get('/{campaign}', [AdminCampaignController::class, 'show'])->name('show');
            Route::put('/{campaign}', [AdminCampaignController::class, 'update'])->name('update');
            Route::delete('/{campaign}', [AdminCampaignController::class, 'destroy'])->name('destroy');
            Route::patch('/{campaign}/active-toggle', [AdminCampaignController::class, 'toggleActive'])->name('active.toggle');
        });

        Route::get('/learning-progress', [AdminLearningProgressController::class, 'index'])->name('learning-progress.index');

        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
            Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
            Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update');
            Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
            Route::patch('/{announcement}/active-toggle', [AdminAnnouncementController::class, 'toggleActive'])->name('active.toggle');
        });

        Route::get('/achievers', [AdminAchieverController::class, 'index'])->name('achievers.index');

        Route::get('/hall-of-fame', [AdminHallOfFameController::class, 'index'])->name('hall-of-fame.index');

        Route::prefix('success-stories')->name('success-stories.')->group(function () {
            Route::get('/', [AdminSuccessStoryController::class, 'index'])->name('index');
            Route::post('/', [AdminSuccessStoryController::class, 'store'])->name('store');
            Route::put('/{successStory}', [AdminSuccessStoryController::class, 'update'])->name('update');
            Route::delete('/{successStory}', [AdminSuccessStoryController::class, 'destroy'])->name('destroy');
            Route::patch('/{successStory}/active-toggle', [AdminSuccessStoryController::class, 'toggleActive'])->name('active.toggle');
        });

        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::post('/', [FaqController::class, 'store'])->name('store');
            Route::put('/{faq}', [FaqController::class, 'update'])->name('update');
            Route::delete('/{faq}', [FaqController::class, 'destroy'])->name('destroy');
            Route::patch('/{faq}/publish-toggle', [FaqController::class, 'togglePublish'])->name('publish.toggle');
        });
        Route::post('/faq-sections', [FaqController::class, 'storeSection'])->name('faq-sections.store');

        Route::prefix('scripts')->name('scripts.')->group(function () {
            Route::get('/', [ScriptTopicController::class, 'index'])->name('index');
            Route::post('/', [ScriptTopicController::class, 'store'])->name('store');
            Route::get('/{script}', [ScriptTopicController::class, 'show'])->name('show');
            Route::put('/{script}', [ScriptTopicController::class, 'update'])->name('update');
            Route::delete('/{script}', [ScriptTopicController::class, 'destroy'])->name('destroy');
            Route::patch('/{script}/publish-toggle', [ScriptTopicController::class, 'togglePublish'])->name('publish.toggle');
        });

        Route::post('/script-topics/{topic}/items', [ScriptItemController::class, 'store'])->name('script-items.store');
        Route::put('/script-items/{item}', [ScriptItemController::class, 'update'])->name('script-items.update');
        Route::delete('/script-items/{item}', [ScriptItemController::class, 'destroy'])->name('script-items.destroy');
        Route::patch('/script-items/{item}/publish-toggle', [ScriptItemController::class, 'togglePublish'])->name('script-items.publish.toggle');
    });

Route::middleware(['auth', 'role:user'])
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/onboarding-assessment', [UserOnboardingAssessmentController::class, 'index'])->name('onboarding-assessment.index');
        Route::post('/onboarding-assessment/quizzes/{quiz}/submit', [UserOnboardingAssessmentController::class, 'submit'])->name('onboarding-assessment.submit');
        Route::get('/my-results', [UserOnboardingAssessmentController::class, 'results'])->name('onboarding-assessment.results');

        Route::get('/resources', [UserResourceController::class, 'index'])->name('resources.index');
        Route::post('/resource-quiz-checkpoints/{checkpoint}/answers', [ResourceQuizAnswerController::class, 'store'])->name('resource-quiz-answers.store');

        Route::get('/documents', [UserDocumentController::class, 'index'])->name('documents.index');

        Route::get('/onboarding-checklist', [UserOnboardingChecklistController::class, 'index'])->name('onboarding-checklist.index');
        Route::patch('/onboarding-checklist/{onboardingChecklistItem}/toggle', [UserOnboardingChecklistController::class, 'toggle'])->name('onboarding-checklist.toggle');

        Route::get('/sales-toolkit', [UserSalesToolkitController::class, 'index'])->name('sales-toolkit.index');

        Route::get('/events', [UserEventController::class, 'index'])->name('events.index');
        Route::post('/events/{event}/register', [UserEventController::class, 'register'])->name('events.register');
        Route::delete('/events/{event}/register', [UserEventController::class, 'unregister'])->name('events.unregister');

        Route::get('/contests', [UserContestController::class, 'index'])->name('contests.index');
        Route::post('/contests/{contest}/register', [UserContestController::class, 'register'])->name('contests.register');
        Route::delete('/contests/{contest}/register', [UserContestController::class, 'unregister'])->name('contests.unregister');

        Route::get('/contest-tracker', [UserContestTrackerController::class, 'index'])->name('contest-tracker.index');
        Route::get('/leaderboard', [UserLeaderboardController::class, 'index'])->name('leaderboard.index');
        Route::get('/incentives', [UserIncentiveController::class, 'index'])->name('incentives.index');

        Route::get('/my-team', [UserTeamController::class, 'index'])->name('team.index');

        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [UserLeadController::class, 'index'])->name('index');
            Route::get('/create', [UserLeadController::class, 'create'])->name('create');
            Route::post('/', [UserLeadController::class, 'store'])->name('store');
            Route::get('/{lead}', [UserLeadController::class, 'show'])->name('show');
            Route::patch('/{lead}/status', [UserLeadController::class, 'updateStatus'])->name('status.update');
            Route::post('/{lead}/notes', [UserLeadController::class, 'storeNote'])->name('notes.store');
        });

        Route::post('/campaigns', [UserLeadController::class, 'storeCampaign'])->name('campaigns.store');

        Route::prefix('products')->name('saas-products.')->group(function () {
            Route::get('/', [UserSaasProductController::class, 'index'])->name('index');
            Route::post('/{saasProduct}/interest', [UserSaasProductController::class, 'toggleInterest'])->name('interest.toggle');
        });

        Route::get('/faqs', [UserFaqController::class, 'index'])->name('faqs.index');

        Route::get('/scripts', [UserScriptController::class, 'index'])->name('scripts.index');

        Route::get('/learning-progress', [UserLearningProgressController::class, 'index'])->name('learning-progress.index');

        Route::get('/announcements', [UserAnnouncementController::class, 'index'])->name('announcements.index');

        Route::get('/my-performance', [UserPerformanceController::class, 'index'])->name('performance.index');

        Route::get('/achievers', [UserAchieverController::class, 'index'])->name('achievers.index');
        Route::get('/hall-of-fame', [UserHallOfFameController::class, 'index'])->name('hall-of-fame.index');
        Route::get('/success-stories', [UserSuccessStoryController::class, 'index'])->name('success-stories.index');

        Route::get('/points', [PointsController::class, 'show'])->name('points.show');

        Route::get('/training', [UserCourseController::class, 'index'])->name('training');
        Route::get('/courses', [UserCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [UserCourseController::class, 'show'])->name('courses.show');

        Route::get('/certificates', [UserCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [UserCertificateController::class, 'show'])->name('certificates.show');

/*
|--------------------------------------------------------------------------
| Certificate Background
|--------------------------------------------------------------------------
*/

Route::get('/certificate-background/{filename}', function ($filename) {

    $path = 'certificates/' . $filename;

    abort_unless(
        Storage::disk('public')->exists($path),
        404
    );

    return Storage::disk('public')->response($path);

})->where('filename', '[A-Za-z0-9._-]+')
  ->name('certificate.background');



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

    Route::post('/issue-types', [UserSupportTicketController::class, 'storeIssueType'])
        ->name('issue-types.store');

});


        });

// Shared with the admin "preview as a client" page (resources/views/user/courses/_player.blade.php),
// so both real students and admins previewing a course can submit checkpoint answers / progress.
Route::middleware(['auth', 'role:user,admin'])
    ->name('user.')
    ->group(function () {
        Route::post('/courses/quiz-checkpoints/{checkpoint}/answers', [CourseQuizAnswerController::class, 'store'])->name('course-quiz-answers.store');
        Route::post('/courses/lessons/{lesson}/progress', [CourseLessonProgressController::class, 'store'])->name('course-lesson-progress.store');
        Route::get('/courses/lessons/{lesson}/video', [CourseLessonVideoController::class, 'show'])->name('course-lesson-video.show');
    });

// Personal to-do/calendar — identical for admin and user accounts (each person
// only ever sees their own tasks), so it's one shared route set rather than
// duplicated admin.*/user.* pairs.
Route::middleware(['auth', 'role:user,admin'])
    ->prefix('tasks')
    ->name('tasks.')
    ->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::post('/bulk-destroy', [TaskController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::patch('/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('toggle');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });


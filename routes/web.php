<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AiInsightController;
use App\Http\Controllers\AnalyticsRecordController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AiDirectorController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ContentApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacebookIntegrationController;
use App\Http\Controllers\GeneratedContentController;
use App\Http\Controllers\MediaPipelineController;
use App\Http\Controllers\PromptTemplateController;
use App\Http\Controllers\PublishingQueueController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/integrations/facebook', [FacebookIntegrationController::class, 'index'])->name('integrations.facebook.index');
    Route::get('/integrations/facebook/connect', [FacebookIntegrationController::class, 'connect'])->name('integrations.facebook.connect');
    Route::get('/integrations/facebook/callback', [FacebookIntegrationController::class, 'callback'])->name('integrations.facebook.callback');
    Route::post('/integrations/facebook/pages/{connection}/publish-test', [FacebookIntegrationController::class, 'publishTest'])->name('integrations.facebook.publish-test');
    Route::post('/integrations/facebook/pages/{connection}/test', [FacebookIntegrationController::class, 'testConnection'])->name('integrations.facebook.test');
    Route::post('/integrations/facebook/pages/{connection}/sync', [FacebookIntegrationController::class, 'sync'])->name('integrations.facebook.sync');
    Route::get('/channels/facebook', [FacebookIntegrationController::class, 'index'])->name('channels.facebook.index');
    Route::post('/channels/facebook/settings', [FacebookIntegrationController::class, 'updateSettings'])->name('channels.facebook.settings');
    Route::get('/channels/facebook/connect', [FacebookIntegrationController::class, 'connect'])->name('channels.facebook.connect');
    Route::get('/channels/facebook/callback', [FacebookIntegrationController::class, 'callback'])->name('channels.facebook.callback');
    Route::post('/channels/facebook/{connection}/publish-test', [FacebookIntegrationController::class, 'publishTest'])->name('channels.facebook.publish-test');
    Route::post('/channels/facebook/{connection}/test', [FacebookIntegrationController::class, 'testConnection'])->name('channels.facebook.test');
    Route::post('/channels/facebook/{connection}/sync', [FacebookIntegrationController::class, 'sync'])->name('channels.facebook.sync');

    Route::resource('workspaces', WorkspaceController::class);
    Route::post('/workspaces/{workspace}/switch', [WorkspaceController::class, 'switch'])->name('workspaces.switch');
    Route::post('/workspaces/{workspace}/members', [WorkspaceMemberController::class, 'store'])->name('workspaces.members.store');
    Route::put('/workspaces/{workspace}/members/{user}', [WorkspaceMemberController::class, 'update'])->name('workspaces.members.update');
    Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceMemberController::class, 'destroy'])->name('workspaces.members.destroy');
    Route::resource('workspaces.brands', BrandController::class);
    Route::resource('workspaces.assets', AssetController::class);
    Route::resource('workspaces.prompts', PromptTemplateController::class)->parameters(['prompts' => 'prompt']);
    Route::post('/workspaces/{workspace}/prompts/{prompt}/duplicate', [PromptTemplateController::class, 'duplicate'])->name('workspaces.prompts.duplicate');
    Route::post('/workspaces/{workspace}/prompts/{prompt}/favorite', [PromptTemplateController::class, 'favorite'])->name('workspaces.prompts.favorite');
    Route::post('/workspaces/{workspace}/prompts/{prompt}/preview', [PromptTemplateController::class, 'preview'])->name('workspaces.prompts.preview');
    Route::post('/workspaces/{workspace}/prompts/{prompt}/rate', [PromptTemplateController::class, 'rate'])->name('workspaces.prompts.rate');
    Route::post('/workspaces/{workspace}/prompts/{prompt}/used', [PromptTemplateController::class, 'markUsed'])->name('workspaces.prompts.used');
    Route::resource('workspaces.contents', GeneratedContentController::class)->parameters(['contents' => 'content']);
    Route::post('/workspaces/{workspace}/contents/{content}/duplicate', [GeneratedContentController::class, 'duplicate'])->name('workspaces.contents.duplicate');
    Route::post('/workspaces/{workspace}/contents/{content}/preview', [GeneratedContentController::class, 'preview'])->name('workspaces.contents.preview');
    Route::post('/workspaces/{workspace}/contents/{content}/submit-review', [ContentApprovalController::class, 'submit'])->name('workspaces.contents.workflow.submit');
    Route::post('/workspaces/{workspace}/contents/{content}/approve', [ContentApprovalController::class, 'approve'])->name('workspaces.contents.workflow.approve');
    Route::post('/workspaces/{workspace}/contents/{content}/reject', [ContentApprovalController::class, 'reject'])->name('workspaces.contents.workflow.reject');
    Route::post('/workspaces/{workspace}/contents/{content}/return', [ContentApprovalController::class, 'return'])->name('workspaces.contents.workflow.return');
    Route::post('/workspaces/{workspace}/contents/{content}/schedule', [ContentApprovalController::class, 'schedule'])->name('workspaces.contents.workflow.schedule');
    Route::post('/workspaces/{workspace}/contents/{content}/publish', [ContentApprovalController::class, 'publish'])->name('workspaces.contents.workflow.publish');
    Route::post('/workspaces/{workspace}/contents/{content}/archive', [ContentApprovalController::class, 'archive'])->name('workspaces.contents.workflow.archive');
    Route::get('/workspaces/{workspace}/publishing', [PublishingQueueController::class, 'index'])->name('workspaces.publishing.index');
    Route::get('/workspaces/{workspace}/publishing/create', [PublishingQueueController::class, 'create'])->name('workspaces.publishing.create');
    Route::post('/workspaces/{workspace}/publishing', [PublishingQueueController::class, 'store'])->name('workspaces.publishing.store');
    Route::get('/workspaces/{workspace}/publishing/{publishing}', [PublishingQueueController::class, 'show'])->name('workspaces.publishing.show');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/cancel', [PublishingQueueController::class, 'cancel'])->name('workspaces.publishing.cancel');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/retry', [PublishingQueueController::class, 'retry'])->name('workspaces.publishing.retry');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/processing', [PublishingQueueController::class, 'processing'])->name('workspaces.publishing.processing');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/published', [PublishingQueueController::class, 'published'])->name('workspaces.publishing.published');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/failed', [PublishingQueueController::class, 'failed'])->name('workspaces.publishing.failed');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/jobs/publish-now', [PublishingQueueController::class, 'publishNow'])->name('workspaces.publishing.jobs.publish-now');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/jobs/schedule', [PublishingQueueController::class, 'scheduleJob'])->name('workspaces.publishing.jobs.schedule');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/jobs/{job}/retry', [PublishingQueueController::class, 'retryJob'])->name('workspaces.publishing.jobs.retry');
    Route::post('/workspaces/{workspace}/publishing/{publishing}/jobs/{job}/cancel', [PublishingQueueController::class, 'cancelJob'])->name('workspaces.publishing.jobs.cancel');
    Route::resource('workspaces.analytics', AnalyticsRecordController::class)->parameters(['analytics' => 'analytics']);
    Route::get('/workspaces/{workspace}/insights', [AiInsightController::class, 'index'])->name('workspaces.insights.index');
    Route::get('/workspaces/{workspace}/insights/create', [AiInsightController::class, 'create'])->name('workspaces.insights.create');
    Route::post('/workspaces/{workspace}/insights', [AiInsightController::class, 'store'])->name('workspaces.insights.store');
    Route::get('/workspaces/{workspace}/insights/{insight}', [AiInsightController::class, 'show'])->name('workspaces.insights.show');
    Route::patch('/workspaces/{workspace}/insights/{insight}/status', [AiInsightController::class, 'updateStatus'])->name('workspaces.insights.status');
    Route::delete('/workspaces/{workspace}/insights/{insight}', [AiInsightController::class, 'destroy'])->name('workspaces.insights.destroy');
    Route::get('/workspaces/{workspace}/pipeline', [MediaPipelineController::class, 'index'])->name('workspaces.pipeline.index');
    Route::post('/workspaces/{workspace}/pipeline', [MediaPipelineController::class, 'store'])->name('workspaces.pipeline.store');
    Route::get('/workspaces/{workspace}/pipeline/{pipeline}', [MediaPipelineController::class, 'show'])->name('workspaces.pipeline.show');
    Route::post('/workspaces/{workspace}/pipeline/{pipeline}/approve', [MediaPipelineController::class, 'approve'])->name('workspaces.pipeline.approve');
    Route::post('/workspaces/{workspace}/pipeline/{pipeline}/reject', [MediaPipelineController::class, 'reject'])->name('workspaces.pipeline.reject');
    Route::post('/workspaces/{workspace}/pipeline/{pipeline}/revision', [MediaPipelineController::class, 'revision'])->name('workspaces.pipeline.revision');
    Route::post('/workspaces/{workspace}/pipeline/{pipeline}/queue', [MediaPipelineController::class, 'queue'])->name('workspaces.pipeline.queue');
    Route::post('/workspaces/{workspace}/pipeline/{pipeline}/publish', [MediaPipelineController::class, 'publish'])->name('workspaces.pipeline.publish');
    Route::post('/workspaces/{workspace}/pipeline/{pipeline}/cancel', [MediaPipelineController::class, 'cancel'])->name('workspaces.pipeline.cancel');
    Route::patch('/workspaces/{workspace}/pipeline/{pipeline}/analytics', [MediaPipelineController::class, 'analytics'])->name('workspaces.pipeline.analytics');
    Route::get('/workspaces/{workspace}/director', AiDirectorController::class)->name('workspaces.director.show');
});

<?php

/**
 * Routes untuk Sistem Validasi Admin SIMPATI
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResearchProjectController;
use App\Http\Controllers\Admin\ValidationController;

// ======================================================
// 🔹 ADMIN VALIDATION ROUTES
// ======================================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Validasi Admin
        Route::get('/validations/dashboard', [ValidationController::class, 'dashboard'])
            ->name('validations.dashboard');

        // Daftar Usulan Pending/Under Review
        Route::get('/validations/pending', [ValidationController::class, 'pending'])
            ->name('validations.pending');

        // Review Detail Proyek
        Route::get('/validations/{id}/review', [ValidationController::class, 'review'])
            ->name('validations.review');

        // Approve Usulan
        Route::post('/validations/{id}/approve', [ValidationController::class, 'approve'])
            ->name('validations.approve');

        // Reject Usulan
        Route::post('/validations/{id}/reject', [ValidationController::class, 'reject'])
            ->name('validations.reject');

        // Request Revision
        Route::post('/validations/{id}/revision', [ValidationController::class, 'requestRevision'])
            ->name('validations.requestRevision');

        // Download Proposal
        Route::get('/validations/{id}/download-proposal', [ValidationController::class, 'downloadProposal'])
            ->name('validations.downloadProposal');

        // View Validation History
        Route::get('/validations/{id}/history', [ValidationController::class, 'history'])
            ->name('validations.history');

        // Export Report
        Route::post('/validations/export', [ValidationController::class, 'exportReport'])
            ->name('validations.export');

        // Bulk Actions
        Route::post('/validations/bulk-action', [ValidationController::class, 'bulkAction'])
            ->name('validations.bulkAction');

        // Search
        Route::get('/validations/search', [ValidationController::class, 'search'])
            ->name('validations.search');

        // Additional routes for complete system

        // Approved Projects List
        Route::get('/validations/approved', [ValidationController::class, 'approved'])
            ->name('validations.approved');

        // Rejected Projects List
        Route::get('/validations/rejected', [ValidationController::class, 'rejected'])
            ->name('validations.rejected');

        // Validation Criteria Management
        Route::get('/validations/criteria', [ValidationController::class, 'criteria'])
            ->name('validations.criteria');

        Route::post('/validations/criteria', [ValidationController::class, 'storeCriteria'])
            ->name('validations.criteria.store');

        Route::put('/validations/criteria/{id}', [ValidationController::class, 'updateCriteria'])
            ->name('validations.criteria.update');

        Route::delete('/validations/criteria/{id}', [ValidationController::class, 'deleteCriteria'])
            ->name('validations.criteria.delete');

        // Approval Letter Templates
        Route::get('/validations/templates', [ValidationController::class, 'templates'])
            ->name('validations.templates');

        Route::get('/validations/templates/create', [ValidationController::class, 'createTemplate'])
            ->name('validations.templates.create');

        Route::post('/validations/templates', [ValidationController::class, 'storeTemplate'])
            ->name('validations.templates.store');

        Route::get('/validations/templates/{id}/edit', [ValidationController::class, 'editTemplate'])
            ->name('validations.templates.edit');

        Route::put('/validations/templates/{id}', [ValidationController::class, 'updateTemplate'])
            ->name('validations.templates.update');

        Route::delete('/validations/templates/{id}', [ValidationController::class, 'deleteTemplate'])
            ->name('validations.templates.delete');

        // Statistics & Analytics
        Route::get('/validations/statistics', [ValidationController::class, 'statistics'])
            ->name('validations.statistics');

        // Validation Scoring (if using scoring system)
        Route::get('/validations/{id}/score', [ValidationController::class, 'showScoring'])
            ->name('validations.score');

        Route::post('/validations/{id}/score', [ValidationController::class, 'submitScore'])
            ->name('validations.score.submit');

    });

// ======================================================
// 🔹 DOSEN ROUTES FOR VALIDATION WORKFLOW
// ======================================================
Route::middleware(['auth', 'role:dosen'])
    ->prefix('dosen')
    ->name('dosen.')
    ->group(function () {

        // Submit proposal for validation
        Route::post('/projects/{id}/submit', [ResearchProjectController::class, 'submitForValidation'])
            ->name('projects.submit');

        // View validation status
        Route::get('/projects/{id}/validation-status', [ResearchProjectController::class, 'validationStatus'])
            ->name('projects.validationStatus');

        // Submit revision
        Route::get('/projects/{id}/revise', [ResearchProjectController::class, 'showRevisionForm'])
            ->name('projects.revise');

        Route::post('/projects/{id}/revise', [ResearchProjectController::class, 'submitRevision'])
            ->name('projects.revise.submit');

        // View validation feedback
        Route::get('/projects/{id}/feedback', [ResearchProjectController::class, 'viewFeedback'])
            ->name('projects.feedback');

        // Download approval letter
        Route::get('/projects/{id}/approval-letter', [ResearchProjectController::class, 'downloadApprovalLetter'])
            ->name('projects.approvalLetter');

    });

<?php
// ============================================================
// IMPLEMENTASI SISTEM VALIDASI ADMIN UNTUK SIMPATI
// ============================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\ProjectValidation;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ValidationController extends Controller
{
    /**
     * Konstruktor - pastikan hanya admin yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Tampilkan dashboard validasi admin
     */
    public function dashboard()
    {
        $stats = [
            'pending' => ResearchProject::where('validation_status', 'submitted')->count(),
            'under_review' => ResearchProject::where('validation_status', 'under_review')->count(),
            'revision_needed' => ResearchProject::where('validation_status', 'revision_needed')->count(),
            'approved_this_month' => ResearchProject::where('validation_status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->count(),
        ];

        $recentSubmissions = ResearchProject::where('validation_status', 'submitted')
            ->with(['ketua', 'createdBy'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $recentApprovals = ResearchProject::where('validation_status', 'approved')
            ->with(['ketua', 'createdBy'])
            ->latest('approved_at')
            ->take(5)
            ->get();

        return view('admin.validations.dashboard', compact('stats', 'recentSubmissions', 'recentApprovals'));
    }

    /**
     * Tampilkan daftar usulan yang menunggu validasi
     */
    public function pending(Request $request)
    {
        $query = ResearchProject::whereIn('validation_status', ['submitted', 'under_review'])
            ->with(['ketua', 'createdBy']);

        // Filter berdasarkan jenis
        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis', $request->jenis);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun_usulan', $request->tahun);
        }

        // Sorting
        $sort = $request->get('sort', 'oldest');
        if ($sort == 'oldest') {
            $query->oldest('submitted_at');
        } else {
            $query->latest('submitted_at');
        }

        $projects = $query->paginate(10)->withQueryString();

        return view('admin.validations.pending', compact('projects'));
    }

    /**
     * Tampilkan detail proyek untuk review
     */
    public function review($id)
    {
        $project = ResearchProject::with([
            'ketua',
            'createdBy',
            'members',
            'images',
            'validations' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Jika belum dalam review, update statusnya
        if ($project->validation_status == 'submitted') {
            $project->validation_status = 'under_review';
            $project->save();

            // Notifikasi ke dosen
            UserNotification::create([
                'user_id' => $project->created_by,
                'content' => "Usulan '{$project->judul}' sedang dalam proses review oleh admin.",
                'type' => 'info',
                'is_shown' => false
            ]);
        }

        // Get validation history
        $validationHistory = ProjectValidation::where('project_id', $id)
            ->with('validator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.validations.review', compact('project', 'validationHistory'));
    }

    /**
     * Proses persetujuan usulan
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_letter' => 'required|file|mimes:pdf|max:5120', // Max 5MB
            'notes' => 'nullable|string|max:1000',
            'nomor_surat' => 'required|string|max:100',
            'tanggal_surat' => 'required|date'
        ]);

        DB::transaction(function () use ($request, $id) {
            $project = ResearchProject::findOrFail($id);

            // Upload surat persetujuan
            $letterPath = $request->file('approval_letter')->store('approval_letters', 'public');

            // Update project status
            $project->validation_status = 'approved';
            $project->status = 'didanai';
            $project->approved_at = now();
            $project->approval_letter = $letterPath;
            $project->nomor_surat_persetujuan = $request->nomor_surat;
            $project->tanggal_surat_persetujuan = $request->tanggal_surat;
            $project->save();

            // Create validation record
            ProjectValidation::create([
                'project_id' => $id,
                'validated_by' => auth()->id(),
                'status' => 'approved',
                'notes' => $request->notes,
                'approval_letter' => $letterPath,
                'validated_at' => now()
            ]);

            // Send notification to dosen
            UserNotification::create([
                'user_id' => $project->created_by,
                'content' => "Selamat! Usulan '{$project->judul}' telah disetujui. Surat persetujuan telah diterbitkan dengan nomor {$request->nomor_surat}.",
                'type' => 'success',
                'is_shown' => false
            ]);

            // Notify team members
            foreach ($project->members as $member) {
                if ($member->id != $project->created_by) {
                    UserNotification::create([
                        'user_id' => $member->id,
                        'content' => "Anda tergabung dalam kegiatan '{$project->judul}' yang telah disetujui.",
                        'type' => 'info',
                        'is_shown' => false
                    ]);
                }
            }
        });

        return redirect()->route('admin.validations.pending')
            ->with('success', 'Usulan berhasil disetujui dan surat persetujuan telah diterbitkan.');
    }

    /**
     * Reject usulan
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:20|max:1000'
        ]);

        DB::transaction(function () use ($request, $id) {
            $project = ResearchProject::findOrFail($id);

            // Update status
            $project->validation_status = 'rejected';
            $project->save();

            // Create validation record
            ProjectValidation::create([
                'project_id' => $id,
                'validated_by' => auth()->id(),
                'status' => 'rejected',
                'notes' => $request->reason,
                'validated_at' => now()
            ]);

            // Notify dosen
            UserNotification::create([
                'user_id' => $project->created_by,
                'content' => "Usulan '{$project->judul}' tidak disetujui. Alasan: {$request->reason}",
                'type' => 'error',
                'is_shown' => false
            ]);
        });

        return redirect()->route('admin.validations.pending')
            ->with('info', 'Usulan telah ditolak.');
    }

    /**
     * Request revision dari dosen
     */
    public function requestRevision(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|min:20|max:1000',
            'revision_points' => 'required|array|min:1',
            'revision_points.*' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($request, $id) {
            $project = ResearchProject::findOrFail($id);

            // Update status
            $project->validation_status = 'revision_needed';
            $project->save();

            // Create validation record with structured revision points
            $revisionData = [
                'notes' => $request->notes,
                'points' => $request->revision_points
            ];

            ProjectValidation::create([
                'project_id' => $id,
                'validated_by' => auth()->id(),
                'status' => 'revision',
                'notes' => json_encode($revisionData),
                'validated_at' => now()
            ]);

            // Build revision message
            $revisionMessage = "Usulan '{$project->judul}' memerlukan revisi:\n";
            $revisionMessage .= $request->notes . "\n\nPoin-poin revisi:\n";
            foreach ($request->revision_points as $index => $point) {
                $revisionMessage .= ($index + 1) . ". " . $point . "\n";
            }

            // Notify dosen
            UserNotification::create([
                'user_id' => $project->created_by,
                'content' => $revisionMessage,
                'type' => 'warning',
                'is_shown' => false
            ]);
        });

        return back()->with('info', 'Permintaan revisi telah dikirim ke dosen.');
    }

    /**
     * Download surat proposal
     */
    public function downloadProposal($id)
    {
        $project = ResearchProject::findOrFail($id);

        if (!$project->surat_proposal || !Storage::disk('public')->exists($project->surat_proposal)) {
            abort(404, 'File proposal tidak ditemukan');
        }

        return Storage::disk('public')->download($project->surat_proposal,
            'Proposal_' . str_replace(' ', '_', $project->judul) . '.pdf');
    }

    /**
     * View validation history
     */
    public function history($id)
    {
        $project = ResearchProject::with(['ketua', 'createdBy'])->findOrFail($id);

        $history = ProjectValidation::where('project_id', $id)
            ->with('validator')
            ->orderBy('validated_at', 'desc')
            ->paginate(10);

        return view('admin.validations.history', compact('project', 'history'));
    }

    /**
     * Export laporan validasi
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:approved,rejected,all'
        ]);

        $query = ResearchProject::with(['ketua', 'createdBy'])
            ->whereBetween('submitted_at', [$request->start_date, $request->end_date]);

        if ($request->status && $request->status != 'all') {
            $query->where('validation_status', $request->status);
        }

        $projects = $query->get();

        // Generate Excel report (menggunakan Laravel Excel package)
        // return Excel::download(new ValidationReportExport($projects), 'laporan_validasi.xlsx');

        // Untuk sementara, return JSON
        return response()->json([
            'periode' => $request->start_date . ' - ' . $request->end_date,
            'total' => $projects->count(),
            'data' => $projects
        ]);
    }

    /**
     * Bulk operations untuk admin
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:research_projects,id',
            'action' => 'required|in:approve,reject,review'
        ]);

        $message = '';

        switch ($request->action) {
            case 'review':
                ResearchProject::whereIn('id', $request->project_ids)
                    ->where('validation_status', 'submitted')
                    ->update(['validation_status' => 'under_review']);
                $message = 'Proyek-proyek terpilih telah dimasukkan ke dalam review.';
                break;

            // Bulk approve dan reject memerlukan konfirmasi tambahan
            // Implementasi lebih lanjut diperlukan
        }

        return back()->with('success', $message);
    }

    /**
     * Search projects untuk validasi
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $projects = ResearchProject::where(function($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('abstrak', 'like', "%{$query}%")
                  ->orWhere('skema', 'like', "%{$query}%");
            })
            ->whereIn('validation_status', ['submitted', 'under_review', 'revision_needed'])
            ->with(['ketua', 'createdBy'])
            ->paginate(10);

        return view('admin.validations.search', compact('projects', 'query'));
    }
}

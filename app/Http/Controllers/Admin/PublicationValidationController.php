<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PublicationStatusChangedMail;
use App\Models\Publication;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PublicationValidationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $sort   = $request->get('sort', 'latest');

        $pubs = Publication::query();

        // Filter status jika diisi
        if ($status && in_array($status, ['draft', 'pending', 'approved', 'revision_requested', 'rejected'], true)) {
            $pubs->where('validation_status', $status);
        }

        // Urutan
        switch ($sort) {
            case 'oldest':
                $pubs->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $pubs->orderBy('judul', 'asc');
                break;
            case 'title_desc':
                $pubs->orderBy('judul', 'desc');
                break;
            case 'latest':
            default:
                $pubs->orderBy('created_at', 'desc');
                break;
        }

        $pubs = $pubs->paginate(20)->withQueryString();

        return view('admin.publications.validation_index', [
            'pubs'          => $pubs,
            'filterStatus'  => $status,
            'filterSort'    => $sort,
        ]);
    }

    public function show(Publication $publication)
    {
        return view('admin.publications.validation_show', compact('publication'));
    }

    public function update(Request $request, Publication $publication)
    {
        // Admin hanya boleh mengubah status dari 'pending'
        if ($publication->validation_status !== 'pending') {
            return redirect()
                ->route('admin.publications.validation.show', $publication)
                ->with('err', 'Publikasi ini sudah pernah divalidasi. Dosen harus mengajukan ulang sebelum status bisa diubah lagi.');
        }

        $data = $request->validate([
            'validation_status' => 'required|string|in:approved,rejected,revision_requested',
            'validation_note'   => 'nullable|string',
        ]);

        $oldStatus = $publication->validation_status;

        $publication->validation_status = $data['validation_status'];
        $publication->validation_note   = $data['validation_note'] ?? null;
        $publication->validated_by      = auth()->id();
        $publication->save();

        // Auto-notifikasi ke dosen hanya kalau status beneran berubah dan punya owner
        if ($publication->owner && $oldStatus !== $publication->validation_status) {
            $owner = $publication->owner;

            $statusLabel = match ($publication->validation_status) {
                'approved'           => 'disetujui',
                'revision_requested' => 'memerlukan revisi',
                'rejected'           => 'ditolak',
                default              => $publication->validation_status,
            };

            // Notifikasi di dropdown (user_notifications)
            UserNotification::create([
                'user_id'    => $owner->id,
                'project_id' => $publication->project_id ?? null,
                'type'       => 'publication_status',
                'message'    => 'Status publikasi "' . ($publication->judul ?? '-') . '" ' . $statusLabel . '.',
            ]);

            // Email ke dosen
            if ($owner->email) {
                Mail::to($owner->email)->send(new PublicationStatusChangedMail($publication));
            }
        }

        return redirect()
            ->route('admin.publications.validation.index')
            ->with('ok', 'Status publikasi berhasil diperbarui.');
    }
}

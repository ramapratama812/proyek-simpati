@php
  $status = $project->validation_status;
@endphp

@if($status === 'approved')
  <span class="badge bg-success">Disetujui</span>
@elseif($status === 'pending')
  <span class="badge bg-warning text-dark">Menunggu Validasi</span>
@elseif($status === 'revision_requested')
  <span class="badge bg-info text-dark">Perlu Revisi</span>
@elseif($status === 'rejected')
  <span class="badge bg-danger">Ditolak</span>
@else
  <span class="badge bg-secondary">Draft</span>
@endif

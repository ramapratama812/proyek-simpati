<div class="mb-3">
    <label>Dosen</label>
    <select name="dosen_id" class="form-control" required>
        <option value="">Pilih Dosen</option>
        @foreach($dosens as $d)
            <option value="{{ $d->id }}" {{ old('dosen_id', $dosenPrestasi->dosen_id ?? '') == $d->id ? 'selected' : '' }}>
                {{ $d->nama }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Judul Prestasi</label>
    <input type="text" name="judul" class="form-control" value="{{ old('judul',$dosenPrestasi->judul ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Kategori</label>
    <input type="text" name="kategori" class="form-control" value="{{ old('kategori',$dosenPrestasi->kategori ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control">{{ old('deskripsi',$dosenPrestasi->deskripsi ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Tahun</label>
    <input type="number" name="tahun" class="form-control" value="{{ old('tahun',$dosenPrestasi->tahun ?? '') }}">
</div>

<div class="mb-3">
    <label>Tingkat</label>
    <input type="text" name="tingkat" class="form-control" value="{{ old('tingkat',$dosenPrestasi->tingkat ?? '') }}">
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="menunggu">Menunggu</option>
        <option value="disetujui">Disetujui</option>
        <option value="ditolak">Ditolak</option>
    </select>
</div>

<div class="mb-3">
    <label>File Bukti</label>
    <input type="file" name="file_bukti" class="form-control">
</div>

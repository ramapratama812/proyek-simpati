<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DosenPrestasiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dosen_id' => 'required|exists:dosens,id',
            'judul' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'nullable',
            'tahun' => 'required|digits:4',
            'tingkat' => 'required',
            'link' => 'nullable|url',
            'status' => 'required|in:menunggu,disetujui,ditolak',
            'file_bukti' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120'
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortofolioController extends Controller
{
    /**
     * Menampilkan halaman portofolio (public page sebelum login)
     */
    public function index()
    {
        // Jika mau menambahkan data dinamis di sini, tinggal tambahkan.
        // Contoh:
        // $projects = ResearchProject::latest()->take(5)->get();
        // return view('portfolio.index', compact('projects'));

        return view('portofolio.index');
    }
}

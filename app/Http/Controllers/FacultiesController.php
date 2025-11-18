<?php
// app/Http/Controllers/FacultiesController.php

namespace App\Http\Controllers;

use App\Models\faculties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultiesController extends Controller
{
    public function index()
    {
        $faculties = faculties::withCount(['prodis'])->orderBy('code')->paginate(20);
        return view('pages.master.faculties.index', compact('faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:1|unique:faculties,code|regex:/^[1-9]$/',
            'name' => 'required|string|max:100',
            'is_active' => 'boolean',
        ], [
            'code.regex' => 'Kode fakultas harus 1-9 (tidak boleh 0)',
        ]);

        try {
            DB::beginTransaction();

            faculties::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return back()->with('success', 'Fakultas berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambah fakultas: ' . $e->getMessage());
        }
    }

    public function update(Request $request, faculties $faculty)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:1|regex:/^[1-9]$/|unique:faculties,code,' . $faculty->id,
            'name' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $faculty->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return back()->with('success', 'Fakultas berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update fakultas: ' . $e->getMessage());
        }
    }

    public function destroy(faculties $faculty)
    {
        // Check if has units - PERBAIKI: sekarang menggunakan faculty_id
        if ($faculty->units()->exists()) {
            return back()->with('error', 'Tidak bisa hapus fakultas yang masih punya prodi/unit!');
        }

        // Check if has accounts
        if ($faculty->accounts()->exists()) {
            return back()->with('error', 'Tidak bisa hapus fakultas yang masih digunakan di akun!');
        }

        try {
            DB::beginTransaction();
            $faculty->delete();
            DB::commit();

            return back()->with('success', 'Fakultas berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus fakultas: ' . $e->getMessage());
        }
    }
}
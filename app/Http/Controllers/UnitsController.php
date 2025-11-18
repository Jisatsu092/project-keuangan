<?php
// app/Http/Controllers/UnitsController.php

namespace App\Http\Controllers;

use App\Models\units;
use App\Models\faculties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitsController extends Controller
{
    public function index()
    {
        $units = units::with('faculty')->orderBy('faculty_id')->orderBy('code')->paginate(50);
        $faculties = faculties::active()->get();
        
        return view('master.units.index', compact('units', 'faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'nullable|exists:faculties,id',
            'code' => 'required|string|size:1|regex:/^[0-9]$/',
            'name' => 'required|string|max:100',
            'type' => 'required|in:prodi,unit_pusat',
            'is_active' => 'boolean',
        ]);

        // Validate unique per faculty
        $exists = units::where('faculty_id', $validated['faculty_id'])
            ->where('code', $validated['code'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kode sudah digunakan di fakultas ini!');
        }

        try {
            DB::beginTransaction();

            units::create([
                'faculty_id' => $validated['faculty_id'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'type' => $validated['type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();
            return back()->with('success', 'units/Prodi berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambah unit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, units $unit)
    {
        $validated = $request->validate([
            'faculty_id' => 'nullable|exists:faculties,id',
            'code' => 'required|string|size:1|regex:/^[0-9]$/',
            'name' => 'required|string|max:100',
            'type' => 'required|in:prodi,unit_pusat',
            'is_active' => 'boolean',
        ]);

        // Validate unique per faculty (except current)
        $exists = units::where('faculty_id', $validated['faculty_id'])
            ->where('code', $validated['code'])
            ->where('id', '!=', $unit->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kode sudah digunakan di fakultas ini!');
        }

        try {
            DB::beginTransaction();

            $unit->update([
                'faculty_id' => $validated['faculty_id'],
                'code' => $validated['code'],
                'name' => $validated['name'],
                'type' => $validated['type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();
            return back()->with('success', 'units/Prodi berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update unit: ' . $e->getMessage());
        }
    }

    public function destroy(units $unit)
    {
        if ($unit->accounts()->exists()) {
            return back()->with('error', 'Tidak bisa hapus unit yang masih digunakan di akun!');
        }

        try {
            DB::beginTransaction();
            $unit->delete();
            DB::commit();

            return back()->with('success', 'units/Prodi berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus unit: ' . $e->getMessage());
        }
    }
}
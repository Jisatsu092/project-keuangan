<?php
// app/Http/Controllers/ActivityTypesController.php

namespace App\Http\Controllers;

use App\Models\activity_types;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityTypesController extends Controller
{
    public function index()
    {
        $activityTypes = activity_types::orderBy('code')->paginate(20);
        return view('pages.master.activity-types.index', compact('activityTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:1|unique:activity_types,code|regex:/^[0-9]$/',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            activity_types::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();
            return back()->with('success', 'Jenis kegiatan berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambah jenis kegiatan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, activity_types $activityType)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:1|regex:/^[0-9]$/|unique:activity_types,code,' . $activityType->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $activityType->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();
            return back()->with('success', 'Jenis kegiatan berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update jenis kegiatan: ' . $e->getMessage());
        }
    }

    public function destroy(activity_types $activityType)
    {
        if ($activityType->accounts()->exists()) {
            return back()->with('error', 'Tidak bisa hapus jenis kegiatan yang masih digunakan di akun!');
        }

        try {
            DB::beginTransaction();
            $activityType->delete();
            DB::commit();

            return back()->with('success', 'Jenis kegiatan berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus jenis kegiatan: ' . $e->getMessage());
        }
    }
}
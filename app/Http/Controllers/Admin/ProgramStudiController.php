<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::with(['faculty'])->withCount('users')->latest()->paginate(10);
        return view('admin.program-studis.index', compact('programStudis'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        return view('admin.program-studis.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:program_studis',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        ProgramStudi::create($data);

        return redirect()->route('admin.program-studis.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(ProgramStudi $programStudi)
    {
        $faculties = Faculty::all();
        return view('admin.program-studis.edit', compact('programStudi', 'faculties'));
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:program_studis,code,' . $programStudi->id,
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        $programStudi->update($data);

        return redirect()->route('admin.program-studis.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        $programStudi->delete();
        return redirect()->route('admin.program-studis.index')->with('success', 'Program Studi berhasil dihapus.');
    }
}

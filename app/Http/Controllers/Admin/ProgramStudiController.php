<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramStudiRequest;
use App\Models\ProgramStudi;
use App\Models\Faculty;
use Illuminate\Http\Request;

use App\Helpers\NavigationHelper;

class ProgramStudiController extends Controller
{
    public function index(Request $request)
    {
        $programStudis = ProgramStudi::with(['faculty'])
            ->withCount('users')
            ->when($request->sort_by, function($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function($q) {
                $q->latest();
            })
            ->paginate(15);
        return view('admin.program-studis.index', compact('programStudis'));
    }

    public function show(Request $request, ProgramStudi $programStudi)
    {
        $programStudi->load(['faculty']);
        
        $students = $programStudi->users()->role('mahasiswa')->latest()->paginate(15, ['*'], 'students_page');
        $lecturers = $programStudi->users()->role(['dosen', 'kaprodi'])->latest()->paginate(15, ['*'], 'lecturers_page');

        $navigation = NavigationHelper::getNavigation($programStudi, null, $request->sort_by, $request->sort_order);

        return view('admin.program-studis.show', compact('programStudi', 'students', 'lecturers', 'navigation'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        return view('admin.program-studis.create', compact('faculties'));
    }

    public function store(ProgramStudiRequest $request)
    {
        $data = $request->validated();

        ProgramStudi::create($data);

        return redirect()->route('admin.program-studis.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(Request $request, ProgramStudi $programStudi)
    {
        $faculties = Faculty::all();
        $navigation = NavigationHelper::getNavigation($programStudi, null, $request->sort_by, $request->sort_order);
        return view('admin.program-studis.edit', compact('programStudi', 'faculties', 'navigation'));
    }

    public function update(ProgramStudiRequest $request, ProgramStudi $programStudi)
    {
        $data = $request->validated();

        $programStudi->update($data);

        return redirect()->route('admin.program-studis.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        $programStudi->delete();
        return redirect()->route('admin.program-studis.index')->with('success', 'Program Studi berhasil dihapus.');
    }
}

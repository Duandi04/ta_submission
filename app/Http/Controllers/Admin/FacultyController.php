<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\Request;

use App\Helpers\NavigationHelper;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $faculties = Faculty::withCount('programStudis')
            ->when($request->sort_by, function($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function($q) {
                $q->latest();
            })
            ->paginate(15);
        return view('admin.faculties.index', compact('faculties'));
    }

    public function show(Request $request, Faculty $faculty)
    {
        $faculty->load(['programStudis' => function($query) {
            $query->withCount(['users' => function($q) {
                $q->role('mahasiswa');
            }, 'users as lecturers_count' => function($q) {
                $q->role(['dosen', 'kaprodi']);
            }]);
        }]);

        $navigation = NavigationHelper::getNavigation($faculty, null, $request->sort_by, $request->sort_order);

        return view('admin.faculties.show', compact('faculty', 'navigation'));
    }

    public function create()
    {
        return view('admin.faculties.create');
    }

    public function store(FacultyRequest $request)
    {
        $data = $request->validated();

        Faculty::create($data);

        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function edit(Request $request, Faculty $faculty)
    {
        $navigation = NavigationHelper::getNavigation($faculty, null, $request->sort_by, $request->sort_order);
        return view('admin.faculties.edit', compact('faculty', 'navigation'));
    }

    public function update(FacultyRequest $request, Faculty $faculty)
    {
        $data = $request->validated();

        $faculty->update($data);

        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}

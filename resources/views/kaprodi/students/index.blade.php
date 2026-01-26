@extends('layouts.app')

@section('title', 'Daftar Seluruh Mahasiswa')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Seluruh Mahasiswa</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Mahasiswa</th>
                            <th>NIM</th>
                            <th>Total Draft</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold text-dark">{{ $student->name }}</div>
                                    <div class="text-muted small">{{ $student->email }}</div>
                                </td>
                                <td>{{ $student->nim_nip }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3">
                                        {{ $student->thesis_submissions_count }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('kaprodi.students.show', $student) }}"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        Lihat Draft
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $students->links() }}
    </div>
@endsection
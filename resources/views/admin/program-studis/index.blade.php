@extends('layouts.app')

@section('title', 'Manajemen Program Studi')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Program Studi</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.program-studis.create') }}" class="btn btn-primary shadow-none">
                <i class="bi bi-plus-circle me-1"></i> Tambah Prodi
            </a>
        </div>
    </div>

    <div id="ajax-container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Nama Program Studi</th>
                                <th>Fakultas</th>
                                <th>Kode</th>
                                <th>Jumlah Mahasiswa/Dosen</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programStudis as $prodi)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $prodi->name }}</td>
                                    <td>{{ $prodi->faculty->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $prodi->code }}</span></td>
                                    <td>{{ $prodi->users_count }}</td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.program-studis.edit', $prodi) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.program-studis.destroy', $prodi) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm-delete
                                                    data-confirm-message="Hapus program studi ini?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted mb-0">Belum ada data program studi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $programStudis->links() }}
        </div>
    </div>
@endsection
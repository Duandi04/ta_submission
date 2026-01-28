@extends('layouts.app')

@section('title', 'Manajemen Fakultas')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Fakultas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary shadow-none">
                <i class="bi bi-plus-circle me-1"></i> Tambah Fakultas
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
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        Nama Fakultas
                                        @if (request('sort_by') == 'name')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                        @else
                                            <i class="bi bi-hash text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'code', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        Kode
                                        @if (request('sort_by') == 'code')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'numeric-down' : 'numeric-up' }}"></i>
                                        @else
                                            <i class="bi bi-hash text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Jumlah Prodi</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $faculty)
                                <tr>
                                    <td class="ps-3 text-muted">
                                        {{ $loop->iteration + ($faculties->currentPage() - 1) * $faculties->perPage() }}
                                    </td>
                                    <td class="fw-semibold">
                                        <a href="{{ route('admin.faculties.show', $faculty) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $faculty->name }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $faculty->code }}</span></td>
                                    <td>{{ $faculty->program_studis_count }}</td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.faculties.show', array_merge(['faculty' => $faculty->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.faculties.edit', array_merge(['faculty' => $faculty->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.faculties.destroy', $faculty) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    data-confirm-delete
                                                    data-confirm-message="Hapus fakultas ini? Seluruh data prodi terkait juga akan terhapus.">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted mb-0">Belum ada data fakultas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $faculties->links() }}
        </div>
    </div>
@endsection

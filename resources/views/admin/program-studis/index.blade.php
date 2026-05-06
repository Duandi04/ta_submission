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
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        Nama Program Studi
                                        @if (request('sort_by') == 'name')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                        @else
                                            <i class="bi bi-hash text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Fakultas</th>
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
                                <th>Jumlah Mahasiswa/Dosen</th>
                                <th>Deadline Pengajuan</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programStudis as $prodi)
                                <tr>
                                    <td class="ps-3 text-muted">
                                        {{ $loop->iteration + ($programStudis->currentPage() - 1) * $programStudis->perPage() }}
                                    </td>
                                    <td class="fw-semibold">
                                        <a href="{{ route('admin.program-studis.show', $prodi) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $prodi->name }}
                                        </a>
                                    </td>
                                    <td>{{ $prodi->faculty->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $prodi->code }}</span></td>
                                    <td>{{ $prodi->users_count }}</td>
                                    <td>
                                        @if($prodi->submission_start || $prodi->submission_end)
                                            <div class="small">
                                                @if($prodi->submission_start)
                                                    <div class="text-success"><i class="bi bi-play-circle me-1"></i>{{ $prodi->submission_start->format('d/m/Y H:i') }}</div>
                                                @endif
                                                @if($prodi->submission_end)
                                                    <div class="text-danger"><i class="bi bi-stop-circle me-1"></i>{{ $prodi->submission_end->format('d/m/Y H:i') }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Tidak dibatasi</span>
                                        @endif
                                    </td>

                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.program-studis.show', array_merge(['program_studi' => $prodi->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.program-studis.edit', array_merge(['program_studi' => $prodi->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.program-studis.destroy', $prodi) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    data-confirm-delete data-confirm-message="Hapus program studi ini?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
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

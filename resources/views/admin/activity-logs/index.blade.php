@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Log Aktivitas Sistem</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small">Dari</label>
                        <input type="date" class="form-control form-control-sm" name="date_from"
                            value="{{ request('date_from') }}" data-auto-submit>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Sampai</label>
                        <input type="date" class="form-control form-control-sm" name="date_to"
                            value="{{ request('date_to') }}" data-auto-submit>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Event</label>
                        <select class="form-select form-select-sm" name="log_name" data-auto-submit>
                            <option value="">Semua</option>
                            @foreach ($logNames as $name)
                                <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                    {{ ucfirst($name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Objek</label>
                        <select class="form-select form-select-sm" name="subject_type" data-auto-submit>
                            <option value="">Semua</option>
                            @foreach ($subjectTypes as $type)
                                <option value="{{ $type }}"
                                    {{ request('subject_type') == $type ? 'selected' : '' }}>
                                    {{ class_basename($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Pengguna</label>
                        <select class="form-select form-select-sm" name="user_id" data-auto-submit>
                            <option value="">Semua</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-1 w-100">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('admin.activity-logs.index') }}"
                                class="btn btn-sm btn-outline-secondary flex-grow-1">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="ajax-container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th width="150">Waktu</th>
                                <th>Pengguna</th>
                                <th>Aktivitas & Perubahan</th>
                                <th>Subject</th>
                                <th width="100">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td class="ps-3 text-muted small">
                                        {{ $loop->iteration + ($activities->currentPage() - 1) * $activities->perPage() }}
                                    </td>
                                    <td>
                                        <small
                                            class="text-muted d-block">{{ $activity->created_at->format('d/m/Y') }}</small>
                                        <small class="fw-bold">{{ $activity->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $activity->causer?->name ?? 'System' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $activity->causer?->email }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span
                                                class="badge bg-info-subtle text-info border border-info-subtle align-self-start">{{ $activity->description }}</span>
                                            @if (isset($activity->properties['attributes']))
                                                <div class="mt-1">
                                                    <ul class="list-unstyled mb-0 small">
                                                        @foreach ($activity->properties['attributes'] as $key => $value)
                                                            @if (!in_array($key, ['updated_at', 'created_at']))
                                                                <li class="mb-1">
                                                                    <span
                                                                        class="text-muted fw-semibold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                    @if (isset($activity->properties['old'][$key]))
                                                                        <span
                                                                            class="text-danger text-decoration-line-through me-1">{{ $activity->properties['old'][$key] }}</span>
                                                                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                                    @endif
                                                                    <span
                                                                        class="text-success fw-bold">{{ $value }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($activity->subject)
                                            <div class="small">
                                                <span
                                                    class="text-muted">{{ class_basename($activity->subject_type) }}</span>
                                                <span class="fw-bold">#{{ $activity->subject_id }}</span>
                                            </div>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $activity->properties['ip'] ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-journal-x display-4 d-block mb-3"></i>
                                        Tidak ada log aktivitas ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $activities->links() }}
        </div>
    </div>
@endsection

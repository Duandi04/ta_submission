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
                    <div class="col-md-3">
                        <label class="form-label small">Dari Tanggal</label>
                        <input type="date" class="form-control form-control-sm" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Sampai Tanggal</label>
                        <input type="date" class="form-control form-control-sm" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Pengguna</label>
                        <select class="form-select form-select-sm" name="user_id">
                            <option value="">Semua Pengguna</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-sm btn-secondary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th width="150">Waktu</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                            <th>Subject</th>
                            <th width="100">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td>
                                    <small>{{ $activity->created_at->format('d M Y H:i:s') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $activity->causer?->name ?? 'System' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $activity->causer?->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $activity->description }}</span>
                                </td>
                                <td>
                                    @if($activity->subject)
                                        <small>
                                            {{ class_basename($activity->subject_type) }}
                                            #{{ $activity->subject_id }}
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $activity->properties['ip'] ?? '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada log aktivitas.</td>
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
@endsection
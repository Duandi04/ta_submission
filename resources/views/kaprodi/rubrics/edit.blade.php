@extends('layouts.app')

@section('title', 'Edit Rubrik Penilaian')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Rubrik: {{ $rubric->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.rubrics.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('kaprodi.rubrics.update', $rubric->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Informasi Rubrik</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Rubrik</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $rubric->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $rubric->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                                {{ $rubric->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Status Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-1"></i> Perbarui Rubrik
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Kriteria Penilaian</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-criterion">
                            <i class="bi bi-plus"></i> Tambah Kriteria
                        </button>
                    </div>
                    <div class="card-body">
                        @error('criteria')
                            <div class="alert alert-danger py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle me-2"></i> {{ $message }}
                            </div>
                        @enderror
                        <div id="criteria-container">
                            @php $criteria = old('criteria', $rubric->criteria); @endphp
                            @foreach ($criteria as $index => $criterion)
                                <div class="criterion-item border rounded p-3 mb-3 bg-light position-relative">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-criterion"
                                        {{ count($criteria) == 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <div class="row">
                                        <div class="col-md-9 mb-3">
                                            <label class="form-label small fw-bold">Nama Kriteria</label>
                                            <input type="text" name="criteria[{{ $index }}][name]"
                                                class="form-control form-control-sm" value="{{ $criterion['name'] }}"
                                                required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label small fw-bold">Bobot (%)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="criteria[{{ $index }}][weight]"
                                                    class="form-control criterion-weight"
                                                    value="{{ $criterion['weight'] }}" required min="0"
                                                    max="100">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Deskripsi Kriteria</label>
                                            <textarea name="criteria[{{ $index }}][description]" class="form-control form-control-sm" rows="2">{{ $criterion['description'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info py-2 d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <span class="small">Total Bobot: <strong id="total-weight">0</strong>% (Harus 100%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('criteria-container');
                const addButton = document.getElementById('add-criterion');
                const totalDisplay = document.getElementById('total-weight');

                function updateTotal() {
                    let total = 0;
                    document.querySelectorAll('.criterion-weight').forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                    totalDisplay.textContent = total;
                    totalDisplay.parentElement.className = total === 100 ? 'text-success' : 'text-danger';
                }

                addButton.addEventListener('click', function() {
                    const index = container.querySelectorAll('.criterion-item').length;
                    const newItem = document.createElement('div');
                    newItem.className = 'criterion-item border rounded p-3 mb-3 bg-light position-relative';
                    newItem.innerHTML = `
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-criterion">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label small fw-bold">Nama Kriteria</label>
                            <input type="text" name="criteria[${index}][name]" class="form-control form-control-sm" required placeholder="Nama kriteria...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Bobot (%)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="criteria[${index}][weight]" class="form-control criterion-weight" required min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi Kriteria</label>
                            <textarea name="criteria[${index}][description]" class="form-control form-control-sm" rows="2" placeholder="Penjelasan..."></textarea>
                        </div>
                    </div>
                `;
                    container.appendChild(newItem);
                    updateRemoveButtons();
                    updateTotal();
                });

                container.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-criterion')) {
                        if (container.querySelectorAll('.criterion-item').length > 1) {
                            e.target.closest('.criterion-item').remove();
                            updateRemoveButtons();
                            updateTotal();
                            reindexItems();
                        }
                    }
                });

                container.addEventListener('input', function(e) {
                    if (e.target.classList.contains('criterion-weight')) {
                        updateTotal();
                    }
                });

                function updateRemoveButtons() {
                    const buttons = container.querySelectorAll('.remove-criterion');
                    buttons.forEach(btn => btn.disabled = buttons.length === 1);
                }

                function reindexItems() {
                    container.querySelectorAll('.criterion-item').forEach((item, index) => {
                        item.querySelectorAll('[name]').forEach(input => {
                            const name = input.getAttribute('name');
                            input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                        });
                    });
                }

                updateTotal();
            });
        </script>
    @endpush
@endsection

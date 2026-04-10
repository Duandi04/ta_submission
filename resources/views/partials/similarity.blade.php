@php
    $inputId = $inputId ?? 'title';
    $excludeId = $excludeId ?? null;
    $isDetailView = $isDetailView ?? false;
@endphp

@if ($isDetailView)
    <div class="card border-warning shadow-sm mb-4" id="similarity-analysis-card" style="display: none;">
        <div class="card-header bg-warning-subtle text-warning-emphasis py-3 border-0">
            <span class="fw-bold"><i class="bi bi-search me-2"></i>Analisis Kesamaan Judul (Orisinalitas)</span>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">Ditemukan beberapa pengajuan dengan judul yang serupa dalam sistem sebagai referensi orisinalitas.</p>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Judul Pengajuan</th>
                            <th>Mahasiswa</th>
                            <th class="text-center">Waktu Upload</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody id="similar-titles-list">
                        <!-- Results injected here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div id="similarity-results" class="mt-2" style="display: none;">
        <div class="card border-warning bg-light">
            <div class="card-body p-3">
                <h6 class="card-title text-warning small mb-2 border-bottom pb-2">
                    <i class="bi bi-search me-1"></i>Analisis Kesamaan Judul:
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0 small">
                        <tbody id="similar-titles-list">
                            <!-- Similar titles will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('{{ $inputId }}');
        const resultsContainer = document.getElementById('similarity-results');
        const resultsList = document.getElementById('similar-titles-list');
        const similarityCard = document.getElementById('similarity-analysis-card');
        const excludeId = '{{ $excludeId }}';
        const isDetailView = {{ $isDetailView ? 'true' : 'false' }};
        let timeout = null;

        function performCheck(title) {
            if (title.length < 5) {
                if (!isDetailView) {
                    resultsList.innerHTML = '<tr><td class="text-muted italic">Ketik judul untuk melihat analisis kesamaan...</td></tr>';
                }
                return;
            }

            fetch('{{ route('similarity.check') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: title,
                    exclude_id: excludeId
                })
            })
            .then(response => response.json())
            .then(data => {
                resultsList.innerHTML = '';
                if (data.count > 0) {
                    data.data.forEach(item => {
                        const tr = document.createElement('tr');
                        const p = item.similarity_percentage;
                        const badgeClass = p >= 70 ? 'bg-danger' : (p >= 40 ? 'bg-warning text-dark' : 'bg-success');
                        
                        if (isDetailView) {
                            tr.innerHTML = `
                                <td>
                                    <div class="fw-medium">${item.title}</div>
                                    <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 0.65rem;">${item.status_label}</span>
                                </td>
                                <td><small class="text-dark">${item.student.name} (${item.student_nim})</small></td>
                                <td class="text-center small text-muted">${item.created_at}</td>
                                <td class="text-center"><span class="badge ${badgeClass}">${p}%</span></td>
                            `;
                        } else {
                            tr.innerHTML = `
                                <td class="ps-0">
                                    <div class="text-dark fw-medium">${item.title}</div>
                                    <div class="text-muted smaller-extra">${item.student.name} • ${item.created_at}</div>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="badge ${badgeClass}">${p}%</span>
                                </td>
                            `;
                        }
                        resultsList.appendChild(tr);
                    });
                    
                    if (resultsContainer) resultsContainer.style.display = 'block';
                    if (similarityCard) similarityCard.style.display = 'block';
                } else {
                    if (isDetailView) {
                        resultsList.innerHTML = '<tr><td colspan="4" class="text-center text-dark fw-bold py-3">Tidak ada judul yang mirip ditemukan.</td></tr>';
                        if (similarityCard) similarityCard.style.display = 'block';
                    } else {
                        resultsList.innerHTML = '<tr><td class="text-dark fw-bold">Tidak ada judul yang mirip ditemukan.</td></tr>';
                        if (resultsContainer) resultsContainer.style.display = 'block';
                    }
                }
            })
            .catch(error => console.error('Error fetching similarity data:', error));
        }

        if (titleInput) {
            titleInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    performCheck(this.value.trim());
                }, 500);
            });

            // Initial check if there's already a value
            if (titleInput.value.trim().length >= 5) {
                performCheck(titleInput.value.trim());
            }
        } else if (isDetailView) {
            // In detail view, we don't have an input, but a static title
            const title = "{{ $submission->title ?? '' }}";
            if (title.length >= 5) {
                performCheck(title);
            }
        }
    });
</script>
@endpush

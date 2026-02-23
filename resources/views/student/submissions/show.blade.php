@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Detail Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'student.submissions.show'])
            <div class="ms-3 btn-group">
                @if ($submission->canBeEditedByStudent())
                    <a href="{{ route('student.submissions.edit', $submission) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif
                <a href="{{ route('student.submissions.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Main Info Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-file-text"></i> Informasi Pengajuan
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Judul</th>
                            <td>: <strong>{{ $submission->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Bidang Penelitian</th>
                            <td>: {{ $submission->research_field ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="200">Pembimbing 1</th>
                            <td>: {{ $submission->supervisor->name ?? 'Belum ditentukan' }}</td>
                        </tr>
                        @if($submission->supervisor_2_id)
                            <tr>
                                <th>Pembimbing 2</th>
                                <td>: {{ $submission->supervisor2->name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>:
                                <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                    {{ $submission->getStatusLabel() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>: {{ $submission->submission_date?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @if ($submission->defense_date)
                            <tr>
                                <th>Tanggal Sidang</th>
                                <td>: {{ $submission->defense_date->format('d/m/Y') }}</td>
                            </tr>
                        @endif
                        @if ($submission->final_score)
                            <tr>
                                <th>Nilai Akhir</th>
                                <td>: <strong class="text-success">{{ $submission->final_score }}</strong></td>
                            </tr>
                        @endif
                    </table>

                    <hr>

                    <h6>Abstrak</h6>
                    <p class="text-justify">{{ $submission->abstract }}</p>

                    @if ($submission->notes)
                        <div class="alert alert-info">
                            <strong><i class="bi bi-sticky"></i> Catatan:</strong><br>
                            {{ $submission->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Files Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-paperclip"></i> File Terlampir</span>
                </div>
                <div class="card-body">
                    @if ($submission->files->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-medical text-primary"></i> Dokumen Utama
                            </h6>
                        </div>
                        @php
                            $mainFiles = $submission->files->whereIn('file_type', [
                                'proposal',
                                'final_document',
                                'presentation',
                            ]);
                            $revisionFiles = $submission->files->where('file_type', 'revision');
                            $proposalFile = $submission->files->where('file_type', 'proposal')->first();
                        @endphp

                        @if ($mainFiles->count() > 0)
                            <div class="list-group mb-4">
                                @foreach ($mainFiles as $file)
                                    <div
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 shadow-sm mb-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-wrapper me-3">
                                                @if (str_contains($file->mime_type, 'pdf'))
                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                                @else
                                                    <i class="bi bi-file-earmark-word-fill text-primary fs-4"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 smaller-text fw-bold text-dark">{{ $file->file_name }}</h6>
                                                <small class="text-muted smaller-extra">
                                                    {{ $file->getFileTypeLabel() }} • {{ $file->getFormattedFileSize() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <a href="{{ route('files.preview', $file) }}"
                                                class="btn btn-sm btn-outline-primary border-0" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file->id) }}"
                                                class="btn btn-sm btn-outline-secondary border-0">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-warning"></i> Riwayat Revisi</h6>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#uploadRevisionModal">
                                <i class="bi bi-plus"></i> Unggah Revisi
                            </button>
                        </div>

                        @if ($revisionFiles->count() > 0)
                            <div class="list-group mb-3">
                                @foreach ($revisionFiles->sortByDesc('created_at') as $file)
                                    <div
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 shadow-sm mb-2 rounded bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-wrapper me-3">
                                                <i class="bi bi-file-earmark-arrow-up-fill text-warning fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 smaller-text fw-bold">{{ $file->file_name }}</h6>
                                                <small class="text-muted smaller-extra">
                                                    {{ $file->created_at->format('d/m/Y H:i') }} •
                                                    {{ $file->getFormattedFileSize() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <a href="{{ route('files.preview', $file) }}"
                                                class="btn btn-sm btn-outline-primary border-0" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file->id) }}"
                                                class="btn btn-sm btn-outline-secondary border-0">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 bg-light rounded border border-dashed">
                                <i class="bi bi-file-earmark-x text-muted fs-1 mb-2 d-block"></i>
                                <p class="text-muted mb-0 small">Belum ada file revisi yang diunggah.</p>
                            </div>
                        @endif

                        <!-- Upload Revision Modal -->
                        <div class="modal fade" id="uploadRevisionModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('student.submissions.revision', $submission->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Unggah File Revisi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-4">
                                                <label class="form-label d-block">Metode Upload <span
                                                        class="text-danger">*</span></label>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="upload_type"
                                                        id="rev_upload_local" value="local" checked autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="rev_upload_local">Lokal</label>

                                                    <input type="radio" class="btn-check" name="upload_type"
                                                        id="rev_upload_drive" value="drive" autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="rev_upload_drive">Drive</label>
                                                </div>
                                            </div>

                                            <div id="rev_local_section">
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih File Revisi (PDF/Doc/Docx)</label>
                                                    <input type="file" name="revision_file" id="revision_file"
                                                        class="form-control">
                                                    <small class="text-muted">Maksimal 10MB</small>
                                                </div>
                                            </div>

                                            <div id="rev_drive_section" style="display: none;">
                                                <div class="mb-3">
                                                    <label class="form-label">File dari Google Drive <span
                                                            class="text-danger">*</span></label>
                                                    <div class="d-grid">
                                                        <button type="button" id="google_picker_btn"
                                                            class="btn btn-outline-dark">
                                                            <i class="bi bi-google me-2"></i> Pilih dari Drive
                                                        </button>
                                                    </div>
                                                    <div id="selected_drive_file" class="mt-2 p-2 border rounded bg-light"
                                                        style="display: none;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-truncate me-2">
                                                                <i class="bi bi-file-earmark-text me-2"></i>
                                                                <span id="drive_file_name" class="fw-medium small"></span>
                                                            </div>
                                                            <button type="button" id="clear_drive_selection"
                                                                class="btn btn-sm btn-link text-danger p-0">Batal</button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="google_file_id" id="google_file_id">
                                                    <input type="hidden" name="google_access_token" id="google_access_token">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Unggah Sekarang</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($proposalFile && str_contains($proposalFile->mime_type, 'pdf'))
                            <div class="pdf-preview-container mt-4">
                                <h6 class="mb-3"><i class="bi bi-eye"></i> Pratinjau Proposal (PDF)</h6>
                                <div class="ratio ratio-16x9 border rounded overflow-hidden shadow-sm" style="height: 600px;">
                                    <iframe src="{{ route('files.preview', $proposalFile) }}#toolbar=0"
                                        title="PDF Preview"></iframe>
                                </div>
                                <div class="mt-2 text-center">
                                    <small class="text-muted">Gunakan tombol 'Lihat' di atas jika pratinjau tidak
                                        muncul.</small>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-x text-muted fs-1 mb-2 d-block"></i>
                            <p class="text-muted mb-0">Belum ada file terlampir.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assessments Card -->
            @if ($submission->assessments->where('is_submitted', true)->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-clipboard-data"></i> Penilaian
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Penilai</th>
                                        <th>Tipe</th>
                                        <th>Nilai</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submission->assessments->where('is_submitted', true) as $assessment)
                                        <tr>
                                            <td>{{ $assessment->evaluator->name }}</td>
                                            <td>{{ $assessment->getEvaluatorTypeLabel() }}</td>
                                            <td><strong>{{ $assessment->total_score }}</strong></td>
                                            <td>{{ $assessment->submitted_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Submit Button (Only for Draft) -->
            @if ($submission->status === 'draft')
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-send"></i> Ajukan Proposal
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Jika Anda yakin dengan draft ini, silakan ajukan untuk direview
                            oleh Kaprodi.</p>
                        <form action="{{ route('student.submissions.submit', $submission) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin mengajukan proposal ini? Proposal yang sudah diajukan tidak dapat diedit kembali sampai ada revisi.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send me-1"></i> Ajukan Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Cancel Submission Button -->
            @if (in_array($submission->status, ['draft', 'submitted']))
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="bi bi-x-circle"></i> Batalkan Pengajuan
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Anda dapat membatalkan pengajuan ini jika masih dalam status Draft
                            atau Sudah Diajukan.</p>
                        <form action="{{ route('student.submissions.cancel', $submission) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://apis.google.com/js/api.js"></script>
    <script>
        const GOOGLE_CLIENT_ID = "{{ env('GOOGLE_CLIENT_ID') }}";
        const GOOGLE_API_KEY = "{{ env('GOOGLE_API_KEY') }}";
        const SCOPES = 'https://www.googleapis.com/auth/drive.readonly';

        let tokenClient;
        let accessToken = null;
        let pickerApiLoaded = false;
        let gapiLoaded = false;

        // Toggle logic for revision modal
        document.querySelectorAll('input[name="upload_type"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'local') {
                    document.getElementById('rev_local_section').style.display = 'block';
                    document.getElementById('rev_drive_section').style.display = 'none';
                    document.getElementById('revision_file').required = true;
                } else {
                    document.getElementById('rev_local_section').style.display = 'none';
                    document.getElementById('rev_drive_section').style.display = 'block';
                    document.getElementById('revision_file').required = false;
                }
            });
        });

        function gapiLoaded_callback() {
            gapi.load('picker', () => {
                pickerApiLoaded = true;
            });
        }

        function gisLoaded_callback() {
            tokenClient = google.accounts.oauth2.initTokenClient({
                client_id: GOOGLE_CLIENT_ID,
                scope: SCOPES,
                callback: (path) => {
                    if (path.error !== undefined) {
                        throw (path);
                    }
                    accessToken = path.access_token;
                    document.getElementById('google_access_token').value = accessToken;
                    createPicker();
                },
            });
            gapiLoaded = true;
        }

        window.onload = function () {
            gapiLoaded_callback();
            gisLoaded_callback();
        };

        const googlePickerBtn = document.getElementById('google_picker_btn');
        if (googlePickerBtn) {
            googlePickerBtn.addEventListener('click', () => {
                if (!GOOGLE_CLIENT_ID || !GOOGLE_API_KEY) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Konfigurasi Google Belum Lengkap',
                        text: 'Silakan atur GOOGLE_CLIENT_ID dan GOOGLE_API_KEY di file .env'
                    });
                    return;
                }

                if (accessToken === null) {
                    tokenClient.requestAccessToken({ prompt: 'consent' });
                } else {
                    createPicker();
                }
            });
        }

        function createPicker() {
            const view = new google.picker.View(google.picker.ViewId.DOCS);
            view.setMimeTypes("application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document");

            const picker = new google.picker.PickerBuilder()
                .enableFeature(google.picker.Feature.NAV_HIDDEN)
                .setDeveloperKey(GOOGLE_API_KEY)
                .setAppId(GOOGLE_CLIENT_ID)
                .setOAuthToken(accessToken)
                .addView(view)
                .setCallback(pickerCallback)
                .build();
            picker.setVisible(true);
        }

        function pickerCallback(data) {
            if (data.action == google.picker.Action.PICKED) {
                const doc = data.docs[0];
                const fileId = doc.id;
                const fileName = doc.name;

                document.getElementById('google_file_id').value = fileId;
                document.getElementById('drive_file_name').innerText = fileName;
                document.getElementById('selected_drive_file').style.display = 'block';
                document.getElementById('google_picker_btn').classList.add('btn-success');
                document.getElementById('google_picker_btn').classList.remove('btn-outline-dark');
                document.getElementById('google_picker_btn').innerHTML = '<i class="bi bi-check-circle me-2"></i> File Terpilih';
            }
        }

        const clearBtn = document.getElementById('clear_drive_selection');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                document.getElementById('google_file_id').value = '';
                document.getElementById('selected_drive_file').style.display = 'none';
                document.getElementById('google_picker_btn').classList.remove('btn-success');
                document.getElementById('google_picker_btn').classList.add('btn-outline-dark');
                document.getElementById('google_picker_btn').innerHTML = '<i class="bi bi-google me-2"></i> Pilih dari Drive';
            });
        }
    </script>
@endpush
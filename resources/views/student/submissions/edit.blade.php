@extends('layouts.app')

@section('title', 'Edit Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Edit Pengajuan Tugas Akhir</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'student.submissions.edit'])
            <div class="ms-3">
                <a href="{{ route('student.submissions.show', $submission) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Form Edit Pengajuan
                </div>
                <div class="card-body">
                    <form action="{{ route('student.submissions.update', $submission) }}" method="POST"
                        enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Tugas Akhir <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title', $submission->title) }}" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstrak <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('abstract') is-invalid @enderror" id="abstract"
                                name="abstract" rows="6" required
                                maxlength="2000">{{ old('abstract', $submission->abstract) }}</textarea>
                            @error('abstract')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="research_field" class="form-label">Bidang Penelitian</label>
                            <input type="text" class="form-control @error('research_field') is-invalid @enderror"
                                id="research_field" name="research_field"
                                value="{{ old('research_field', $submission->research_field) }}" maxlength="100">
                            @error('research_field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Metode Upload File Baru (Opsional)</label>
                            <div class="btn-group w-100" role="group" aria-label="Upload method toggle">
                                <input type="radio" class="btn-check" name="upload_type" id="upload_local" value="local" {{ old('upload_type', 'local') === 'local' ? 'checked' : '' }} autocomplete="off">
                                <label class="btn btn-outline-primary" for="upload_local">
                                    <i class="bi bi-laptop me-2"></i>Upload dari Komputer
                                </label>

                                <input type="radio" class="btn-check" name="upload_type" id="upload_drive" value="drive" {{ old('upload_type') === 'drive' ? 'checked' : '' }} autocomplete="off">
                                <label class="btn btn-outline-primary" for="upload_drive">
                                    <i class="bi bi-google me-2"></i>Pilih dari Google Drive
                                </label>
                            </div>
                            @error('upload_type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="local_upload_section"
                            style="{{ old('upload_type', 'local') === 'local' ? '' : 'display: none;' }}">
                            <div class="mb-3">
                                <label for="proposal_file" class="form-label">File Proposal</label>
                                <input type="file" class="form-control @error('proposal_file') is-invalid @enderror"
                                    id="proposal_file" name="proposal_file" accept=".pdf,.doc,.docx">
                                @error('proposal_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah file. Format: PDF, DOC, DOCX.
                                    Maksimal 10MB.</small>
                            </div>
                        </div>

                        <div id="drive_upload_section" style="{{ old('upload_type') === 'drive' ? '' : 'display: none;' }}">
                            <div class="mb-3">
                                <label class="form-label">File dari Google Drive</label>
                                <div class="d-grid">
                                    <button type="button" id="google_picker_btn" class="btn btn-outline-dark">
                                        <i class="bi bi-google me-2"></i> Hubungkan ke Google Drive
                                    </button>
                                </div>
                                <div id="selected_drive_file" class="mt-2 p-2 border rounded bg-light"
                                    style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            <span id="drive_file_name" class="fw-medium"></span>
                                        </div>
                                        <button type="button" id="clear_drive_selection"
                                            class="btn btn-sm btn-link text-danger">Batal</button>
                                    </div>
                                </div>
                                <input type="hidden" name="google_file_id" id="google_file_id"
                                    value="{{ old('google_file_id') }}">
                                <input type="hidden" name="google_access_token" id="google_access_token"
                                    value="{{ old('google_access_token') }}">
                                @error('google_file_id')
                                    <div class="text-danger small mt-1">Silakan pilih file dari Google Drive.</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('student.submissions.show', $submission) }}" class="btn btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="bi bi-exclamation-triangle"></i> Perhatian
                </div>
                <div class="card-body">
                    <p class="small">
                        <strong>Status saat ini:</strong>
                        <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                            {{ $submission->getStatusLabel() }}
                        </span>
                    </p>
                    <p class="small">
                        Anda hanya dapat mengedit pengajuan dengan status <strong>Draft</strong> atau <strong>Perlu
                            Revisi</strong>.
                    </p>
                    <hr>
                    <h6 class="small">File Saat Ini:</h6>
                    <ul class="small">
                        @foreach ($submission->files as $file)
                            <li>{{ $file->file_name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
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

        document.querySelectorAll('input[name="upload_type"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'local') {
                    document.getElementById('local_upload_section').style.display = 'block';
                    document.getElementById('drive_upload_section').style.display = 'none';
                } else {
                    document.getElementById('local_upload_section').style.display = 'none';
                    document.getElementById('drive_upload_section').style.display = 'block';
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

        document.getElementById('clear_drive_selection').addEventListener('click', () => {
            document.getElementById('google_file_id').value = '';
            document.getElementById('selected_drive_file').style.display = 'none';
            document.getElementById('google_picker_btn').classList.remove('btn-success');
            document.getElementById('google_picker_btn').classList.add('btn-outline-dark');
            document.getElementById('google_picker_btn').innerHTML = '<i class="bi bi-google me-2"></i> Hubungkan ke Google Drive';
        });
    </script>
@endpush
{{-- Photo Upload Partial --}}
{{-- Usage: @include('partials.photo-upload', ['user' => $user, 'size' => 120]) --}}
@php
    $photoSize = $size ?? 120;
    $inputId = 'profile_photo_input_' . ($user->id ?? 'new');
@endphp

<div class="text-center mb-4">
    <div class="position-relative d-inline-block">
        <img id="photo_preview_{{ $inputId }}" src="{{ $user->profile_photo_url }}"
            class="rounded-circle img-thumbnail shadow-sm"
            style="width: {{ $photoSize }}px; height: {{ $photoSize }}px; object-fit: cover; cursor: pointer;"
            alt="Foto Profil" title="Klik untuk ganti foto" onclick="document.getElementById('{{ $inputId }}').click()">
        <label for="{{ $inputId }}"
            class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow"
            style="width: 32px; height: 32px; cursor: pointer; border: 2px solid white;">
            <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
        </label>
    </div>
    <input type="file" class="d-none @error('profile_photo') is-invalid @enderror" id="{{ $inputId }}"
        name="profile_photo" accept="image/jpeg,image/png,image/webp">
    @error('profile_photo')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div class="small text-muted mt-2">JPG, PNG, atau WebP. Maks 2MB.</div>

    @if($user->profile_photo)
        <div class="mt-2">
            <label class="form-check-label text-danger small" style="cursor: pointer;">
                <input type="checkbox" name="remove_photo" value="1" class="form-check-input form-check-input-sm"> Hapus
                foto
            </label>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.getElementById('{{ $inputId }}').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                e.target.value = '';
                return;
            }

            // Validate type
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Format file tidak valid. Gunakan JPG, PNG, atau WebP.');
                e.target.value = '';
                return;
            }

            // Live preview
            const reader = new FileReader();
            reader.onload = function (event) {
                document.getElementById('photo_preview_{{ $inputId }}').src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
@endpush
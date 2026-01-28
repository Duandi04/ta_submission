@if (isset($navigation))
    <div class="btn-group shadow-sm ms-3">
        <a id="nav-prev"
            href="{{ $navigation['prev'] ? route($route, array_merge([$navigation['prev']], $navigation['query'])) : '#' }}"
            class="btn btn-sm btn-white border {{ !$navigation['prev'] ? 'disabled' : '' }}" title="Sebelumnya (Alt + ←)">
            <i class="bi bi-chevron-left"></i>
        </a>
        <span class="btn btn-sm btn-white border disabled text-dark fw-bold">
            {{ $navigation['current'] }} / {{ $navigation['total'] }}
        </span>
        <a id="nav-next"
            href="{{ $navigation['next'] ? route($route, array_merge([$navigation['next']], $navigation['query'])) : '#' }}"
            class="btn btn-sm btn-white border {{ !$navigation['next'] ? 'disabled' : '' }}"
            title="Selanjutnya (Alt + →)">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <script>
        document.addEventListener('keydown', function(event) {
            // Use Alt + Left/Right to avoid conflict with text input
            if (event.altKey) {
                if (event.key === 'ArrowLeft') {
                    const prev = document.getElementById('nav-prev');
                    if (prev && !prev.classList.contains('disabled') && prev.getAttribute('href') !== '#') {
                        window.location.href = prev.href;
                    }
                } else if (event.key === 'ArrowRight') {
                    const next = document.getElementById('nav-next');
                    if (next && !next.classList.contains('disabled') && next.getAttribute('href') !== '#') {
                        window.location.href = next.href;
                    }
                }
            }
        });
    </script>
@endif

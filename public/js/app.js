// JavaScript for Sistem Pengajuan TA - Modern & Performance Oriented

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle Logic
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Load sidebar state from localStorage
    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        sidebar?.classList.add('collapsed');
        mainContent?.classList.add('expanded');
    }

    sidebarToggle?.addEventListener('click', function() {
        sidebar?.classList.toggle('collapsed');
        mainContent?.classList.toggle('expanded');
        
        // Remove the initialization class once user starts interacting
        document.documentElement.classList.remove('sidebar-collapsed-init');
        
        // Save state to localStorage
        if (sidebar?.classList.contains('collapsed')) {
            localStorage.setItem('sidebarState', 'collapsed');
        } else {
            localStorage.setItem('sidebarState', 'expanded');
        }
    });

    // Auto-hide alerts after 5 seconds (only for success messages, other info/danger/warning will persist)
    const alerts = document.querySelectorAll('.alert-success:not(.alert-persistent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = this.nextElementSibling;
                if (label && label.classList.contains('custom-file-label')) {
                    label.textContent = fileName;
                }
            }
        });
    });

    // Confirm delete actions with SweetAlert2
    const deleteButtons = document.querySelectorAll('[data-confirm-delete], .btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const message = this.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin menghapus data ini?';

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-secondary px-4 me-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    if (form) {
                        form.submit();
                    } else if (this.tagName === 'A') {
                        window.location.href = this.href;
                    }
                }
            });
        });
    });

    // Global Form Confirmation Handler (using data-confirm)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const confirmMessage = form.getAttribute('data-confirm');
        
        if (confirmMessage && !form.dataset.confirmed) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Tindakan',
                text: confirmMessage,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary px-4',
                    cancelButton: 'btn btn-secondary px-4 me-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
            });
        }
    });

    // AJAX Real-time Search & Filtering Logic
    const ajaxSubmit = (form) => {
        const formData = new FormData(form);
        const searchParams = new URLSearchParams(formData);
        const url = `${form.action}?${searchParams.toString()}`;

        // Update URL
        window.history.pushState({}, '', url);

        // Fetch new content
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#ajax-container');
            const currentContainer = document.querySelector('#ajax-container');

            if (newContent && currentContainer) {
                currentContainer.innerHTML = newContent.innerHTML;
                
                // Re-initialize any dynamic elements in the new content
                // (e.g., tooltips, delete buttons)
                initDeleteButtons();
                initTooltips();
                document.dispatchEvent(new Event('ajaxContentLoaded'));
            }
        })
        .catch(error => console.error('Error during search:', error));
    };

    const searchInputs = document.querySelectorAll('[data-auto-search], input[name="search"]');
    let searchTimeout;

    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const form = this.closest('form');
            if (form) {
                searchTimeout = setTimeout(() => {
                    ajaxSubmit(form);
                }, 500); // 500ms debounce
            }
        });

        // Prevent form submission on Enter so it doesn't reload the page
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const form = this.closest('form');
                if (form) ajaxSubmit(form);
            }
        });
    });

    const autoSubmitElements = document.querySelectorAll('[data-auto-submit]');
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) ajaxSubmit(form);
        });
    });

    // Encapsulate delete buttons initialization for re-use after AJAX
    function initDeleteButtons() {
        // Confirm delete actions with SweetAlert2
        const deleteButtons = document.querySelectorAll('[data-confirm-delete], .btn-delete');
        deleteButtons.forEach(button => {
            // Remove existing listener to prevent duplicates if initialization is called multiple times
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const message = this.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin menghapus data ini?';

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger px-4',
                        cancelButton: 'btn btn-secondary px-4 me-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (form) {
                            form.submit();
                        } else if (this.tagName === 'A') {
                            window.location.href = this.href;
                        }
                    }
                });
            });
        });
    }

    // Encapsulate tooltips initialization
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initial call
    initDeleteButtons();
    initTooltips();
});

// Helper functions for loading states
function showLoading() {
    const overlay = document.createElement('div');
    overlay.className = 'spinner-overlay';
    overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.querySelector('.spinner-overlay');
    if (overlay) overlay.remove();
}

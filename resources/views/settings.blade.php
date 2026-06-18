@extends('layouts.app')

@section('title', __('Settings') . ' - EduPlex')
@section('page-title', __('Settings'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold">{{ __('Appearance & Language') }}</h5>
            </div>
            <div class="card-body p-4">
                
                <!-- Dark Mode Toggle -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h6 class="mb-1 fw-bold">{{ __('Dark Mode') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Switch between light and dark themes for the dashboard.') }}</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="darkModeToggle" style="width: 3em; height: 1.5em;" onchange="toggleCustomColors()">
                    </div>
                </div>

                <!-- Custom Dark Mode Colors (Initially Hidden if Light) -->
                <div id="customColorSection" class="mt-4 p-3 rounded-3 bg-light border" style="display: none;">
                    <h6 class="mb-3 fw-bold small text-uppercase text-muted">{{ __('Custom Dark Mode Colors') }}</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Background Body') }}</label>
                            <input type="color" class="form-control form-control-color w-100" id="colorBgBody" value="#0f172a">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Card Background') }}</label>
                            <input type="color" class="form-control form-control-color w-100" id="colorCardBg" value="#1e293b">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Sidebar Background') }}</label>
                            <input type="color" class="form-control form-control-color w-100" id="colorSidebarBg" value="#1e293b">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Main Text Color') }}</label>
                            <input type="color" class="form-control form-control-color w-100" id="colorTextMain" value="#f1f5f9">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-secondary" onclick="resetDarkColors()">{{ __('Reset to Default') }}</button>
                    </div>
                </div>

                <hr class="my-4 opacity-50">

                <!-- Language Selection -->
                <div class="mb-4">
                    <h6 class="mb-2 fw-bold">{{ __('Language') }}</h6>
                    <p class="text-muted small mb-3">{{ __('Select your preferred language for the interface.') }}</p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="lang-option p-3 border rounded-3 text-center cursor-pointer" data-lang="en">
                                <span class="fs-4 d-block mb-1">🇺🇸</span>
                                <span class="fw-semibold">{{ __('English') }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="lang-option p-3 border rounded-3 text-center cursor-pointer" data-lang="km">
                                <span class="fs-4 d-block mb-1">🇰🇭</span>
                                <span class="fw-semibold">{{ __('Khmer') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button class="btn btn-primary px-4 rounded-3" onclick="saveSettings()">
                        {{ __('Save Changes') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .lang-option { transition: all 0.2s; }
    .lang-option:hover { background: #f8f9fa; border-color: #dee2e6; }
    .lang-option.active { border-color: #3b82f6; background: rgba(59, 130, 246, 0.05); color: #3b82f6; }
    
    /* Dark mode adjustments for settings page */
    [data-bs-theme="dark"] .card { background-color: #1e293b; color: #f1f5f9; }
    [data-bs-theme="dark"] .card-header { background-color: #1e293b !important; border-bottom-color: #334155 !important; }
    [data-bs-theme="dark"] .text-muted { color: #94a3b8 !important; }
    [data-bs-theme="dark"] .lang-option:hover { background: #334155; }
    [data-bs-theme="dark"] .lang-option { border-color: #334155; }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load Dark Mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (currentTheme === 'dark') {
            darkModeToggle.checked = true;
            toggleCustomColors();
        }

        // Load Custom Colors
        const customColors = JSON.parse(localStorage.getItem('dark_mode_colors') || '{}');
        if (customColors.bgBody) document.getElementById('colorBgBody').value = customColors.bgBody;
        if (customColors.cardBg) document.getElementById('colorCardBg').value = customColors.cardBg;
        if (customColors.sidebarBg) document.getElementById('colorSidebarBg').value = customColors.sidebarBg;
        if (customColors.textMain) document.getElementById('colorTextMain').value = customColors.textMain;

        // Load Language
        const currentLang = localStorage.getItem('lang') || 'en';
        document.querySelectorAll('.lang-option').forEach(opt => {
            opt.classList.remove('active');
            if (opt.dataset.lang === currentLang) {
                opt.classList.add('active');
            }
            
            opt.addEventListener('click', function() {
                document.querySelectorAll('.lang-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });

    function toggleCustomColors() {
        const isDark = document.getElementById('darkModeToggle').checked;
        const section = document.getElementById('customColorSection');
        section.style.display = isDark ? 'block' : 'none';
    }

    function resetDarkColors() {
        document.getElementById('colorBgBody').value = '#0f172a';
        document.getElementById('colorCardBg').value = '#1e293b';
        document.getElementById('colorSidebarBg').value = '#1e293b';
        document.getElementById('colorTextMain').value = '#f1f5f9';
    }

    function saveSettings() {
        const isDark = document.getElementById('darkModeToggle').checked;
        const activeLang = document.querySelector('.lang-option.active').dataset.lang;

        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        localStorage.setItem('lang', activeLang);

        if (isDark) {
            const colors = {
                bgBody: document.getElementById('colorBgBody').value,
                cardBg: document.getElementById('colorCardBg').value,
                sidebarBg: document.getElementById('colorSidebarBg').value,
                textMain: document.getElementById('colorTextMain').value
            };
            localStorage.setItem('dark_mode_colors', JSON.stringify(colors));
        }

        // Apply theme immediately
        document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        
        Swal.fire({
            icon: 'success',
            title: "{{ __('Settings Saved') }}",
            text: "{{ __('Your preferences have been updated.') }}",
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            // Redirect to switch language on backend
            window.location.href = `/lang/${activeLang}`;
        });
    }
</script>
@endpush

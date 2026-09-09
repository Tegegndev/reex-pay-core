@php
    $storageWarningTitle = __('Storage Link Missing');
    $storageWarningMsg = __('No images, files, or previews will display anywhere (admin or user) until the storage link is enabled.');
    $storageCommand = 'php artisan storage:link';
    $storageHelp = __('To fix: Open your server terminal, go to the following folder, then run this command:');
    $storageDocs = __('Need help? See the official Laravel docs:');
    $cwd = base_path();
@endphp

@if(!is_link(public_path('storage')) && !file_exists(public_path('storage')))
    <div class="alert alert-danger d-flex align-items-center gap-3 p-4 mb-4 shadow-sm" role="alert" style="border-left: 5px solid #e55353;">
        <div>
            <i class="bi bi-exclamation-triangle-fill fs-2 text-danger me-2"></i>
        </div>
        <div>
            <strong class="d-block mb-1">{{ $storageWarningTitle }}</strong>
            <div class="mb-2">{{ $storageWarningMsg }}</div>
            <div class="mb-2">
                <span class="fw-semibold">{{ $storageHelp }}</span>
                <div class="mb-1">
                    <span class="text-muted">{{ __('Project Folder') }}:</span> <code>{{ $cwd }}</code>
                </div>
                <code class="bg-light border rounded px-2 py-1 d-inline-block mt-1">{{ $storageCommand }}</code>
            </div>
            <div class="mb-2">
                <span class="text-muted">{{ __('Storage Link Path') }}:</span> <code>{{ public_path('storage') }}</code>
            </div>
            <div>
                <a href="https://laravel.com/docs/11.x/filesystem#the-public-disk" target="_blank" rel="noopener" class="link-primary text-decoration-underline">{{ $storageDocs }}</a>
            </div>
        </div>
    </div>
@endif
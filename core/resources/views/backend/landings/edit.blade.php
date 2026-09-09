@extends('backend.layouts.app')
@section('title', 'Edit Landing Page')

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between w-100 flex-wrap">
      <div class="mb-3 mb-lg-0">
        <h1 class="h4">@lang('Edit Custom Landing Page')</h1>
      </div>
      <div class="btn-toolbar mb-md-0 mb-2">
        <a href="{{ route('admin.custom-landing.index') }}" class="btn btn-primary">
          <x-icon name="back" height="20" width="20" class="me-1"/>
          {{ __('Back') }}
        </a>
      </div>
    </div>
  </div>
  
  <div class="card shadow-sm">
    <div class="card-body">
      <form action="{{ route('admin.custom-landing.update', $landing_page->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="form-group mb-4">
          <label for="name" class="form-label">@lang('Landing Page Name')</label>
          <input type="text" id="name" name="name" value="{{ $landing_page->name }}" class="form-control" placeholder="@lang('Enter a unique name for the custom landing page')" required>
          <small class="form-text text-muted">@lang('This name will help you identify the custom landing page in the list.')</small>
        </div>
        
        <div class="form-group mb-4">
          <label class="form-label">@lang('Upload ZIP File')</label>
          <div id="drop-area" class="border rounded p-4 text-center pointer-event">
            <p id="file-name" class="mb-0 text-muted"><i class="fa fa-upload me-2"></i> @lang('Drag & Drop your ZIP file here or click to upload')</p>
            <input type="file" id="zipFile" name="zipFile" class="form-control-file" accept=".zip" multiple style="display:none;">
          </div>
          <div class="alert alert-warning mt-3">
            <strong>@lang('Important:')</strong> @lang('Uploading new files will replace the existing landing page files.')
          </div>
        </div>
        <div class="form-group mb-4">
          <label class="form-label" for="status">{{ __('Status') }}</label>
          <div class="form-check form-switch">
            <input type="hidden" name="status" value="0">
            <input class="form-check-input coevs-switch" type="checkbox" name="status" value="1" @checked($landing_page->status)>
          </div>
        </div>
        
        <div class="text-end">
          <button class="btn btn-primary"><x-icon name="check" height="20" width="20" class="me-2"/>@lang('Update Landing Page')</button>
        </div>
      </form>
    </div>
  </div>
@endsection
  @push('scripts')
    <script>
      const dropArea = document.getElementById('drop-area');
      const fileInput = document.getElementById('zipFile');
      const fileNameDisplay = document.getElementById('file-name');
      
      dropArea.addEventListener('click', () => fileInput.click());
      
      dropArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropArea.classList.add('bg-light');
      });
      
      dropArea.addEventListener('dragleave', () => dropArea.classList.remove('bg-light'));
      
      dropArea.addEventListener('drop', (event) => {
        event.preventDefault();
        dropArea.classList.remove('bg-light');
        fileInput.files = event.dataTransfer.files;
        updateFileName();
      });
      
      fileInput.addEventListener('change', updateFileName);
      
      function updateFileName() {
        const files = fileInput.files;
        if (files.length > 0) {
          fileNameDisplay.textContent = files[0].name;
        }
      }
    </script>
  @endpush
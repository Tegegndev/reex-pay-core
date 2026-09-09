@extends('backend.layouts.app')
@section('title', __('Upload Custom Landing Page'))

@section('content')
  <div class="py-4">
    <div class="d-flex justify-content-between w-100 flex-wrap">
      <div class="mb-3 mb-lg-0">
        <h1 class="h4">@lang('Upload New Custom Landing Page')</h1>
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
      <form action="{{ route('admin.custom-landing.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-4">
          <label for="name" class="form-label">@lang('Landing Page Name')</label>
          <input type="text" id="name" name="name" class="form-control" placeholder="@lang('Enter a unique name for the custom landing page')" required>
          <small class="form-text text-muted">@lang('This name will help you identify the custom landing page in the list.')</small>
        </div>
        
        <div class="form-group mb-4">
          <label class="form-label">@lang('Upload ZIP File')</label>
          <div id="drop-area" class="border rounded p-4 text-center pointer-event">
            <p id="file-name" class="mb-0 text-muted"><i class="fa fa-upload me-2"></i> @lang('Drag & Drop your ZIP file here or click to upload')</p>
            <input type="file" id="zipFile" name="zipFile" class="form-control-file" accept=".zip" required style="display:none;">
          </div>
          <div class="alert alert-warning mt-3">
            <strong>@lang('Important:')</strong> @lang('Uploading a new landing page will disable the current default landing page and make this the active one.')
          </div>
          <div class="alert alert-info border-0 rounded-2 mb-2" style="background: #eef6fb; color: #055160; font-size: 14px;">
            <div class="title">
              ⚡ {{ __('Folder Structure') }}
            </div>
            <div class="description">
<pre class="bg-dark text-white p-3 rounded" style="font-family: 'Courier New', Courier, monospace;">
  /index.html
  /css/
  /js/
  /images/
</pre>
              {{ __('Ensure that all files are correctly placed. The main HTML file should be named index.html and placed at the root of the ZIP file.') }}
              <ul>
                <li>{{ __('CSS files should be in the /css directory.') }}</li>
                <li>{{ __('JavaScript files should be in the /js directory.') }}</li>
                <li>{{ __('Images should be in the /images directory.') }}</li>
              </ul>
              <p>{{ __('Example of how to reference files in index.html:') }}</p>
              <pre class="bg-dark text-white p-3 rounded" style="font-family: 'Courier New', Courier, monospace;">
&lt;link rel="stylesheet" href="/custom-landings/{folder}/css/your-style-file.css" /&gt;

&lt;img src="/custom-landings/{folder}/images/your-image-file.jpg" alt="Description" /&gt;

&lt;script src="/custom-landings/{folder}/js/your-script-file.js"&gt;&lt;/script&gt;
      </pre>
            </div>
          </div>        </div>
        
        <div class="text-end">
          <button class="btn btn-primary"><x-icon name="check" height="20" width="20" class="me-2"/>@lang('Upload Custom Landing Page')</button>
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
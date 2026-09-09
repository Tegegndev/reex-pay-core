<div class="card border-0 shadow-sm mb-4">
	<div class="card-body">
		<form method="POST" action="{{ route('admin.page.site.store') }}" enctype="multipart/form-data">
			@csrf
			
			<div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap">
				<h2 class="h5 mb-0">{{ __('Page Elements') }}</h2>
				
				<div class="d-flex flex-column flex-sm-row flex-wrap align-items-center justify-content-center text-center gap-3">
					<div class="form-check form-switch">
						<label class="form-check-label me-2" for="is_breadcrumb">{{ __('Page Breadcrumb') }}</label>
						<input class="form-check-input coevs-switch me-2" type="checkbox" value="1" name="is_breadcrumb" id="is_breadcrumb">
					</div>
					
					<div class="form-check form-switch ">
						<label class="form-check-label me-2" for="flexSwitchCheckDefault">{{ __('Page Status') }}</label>
						<input class="form-check-input coevs-switch me-2" type="checkbox" value="1" name="is_active" id="flexSwitchCheckDefault" checked>
					</div>
				</div>
			</div>
			
			<div class="sortable-list drop-here mt-2 mb-3" id="pageComponent">
                <span class="text-muted drop-text">
                    <i class="fa fa-upload me-2"></i> {{ __('Drop Component Here') }}
                </span>
			</div>
			
			
			<div class="col-md-12">
				{{-- Language Tabs --}}
				<ul class="nav nav-tabs mb-3" role="tablist">
					@foreach($locales as $locale => $label)
						<li class="nav-item" role="presentation">
							<button class="nav-link {{ $loop->first ? 'active' : '' }}"
							        id="page-title-tab-{{ $locale }}"
							        data-coreui-toggle="tab"
							        data-coreui-target="#page-title-{{ $locale }}"
							        type="button" role="tab">
								{{ $label }}
							</button>
						</li>
					@endforeach
				</ul>
				
				{{-- Language Tab Content --}}
				<div class="tab-content">
					@foreach($locales as $locale => $label)
						<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="page-title-{{ $locale }}" role="tabpanel">
							<div class="mb-3">
								<label for="name_{{ $locale }}" class="form-label">{{ __('Page Title') }}
									<small class="text-muted text-uppercase">({{ $locale }})</small>
								</label>
								<input type="text"
								       id="page_title_{{ $locale }}"
								       name="page_title[{{ $locale }}]"
								       class="form-control {{ $locale == app()->getDefaultLocale() ? 'title-to-slug' : '' }}"
								       {{ $locale == app()->getDefaultLocale() ? 'data-slug-target=#page_slug' : '' }}
								       value="{{ old('page_title.' . $locale) }}"
								       placeholder="{{ __('Enter page title for :lang', ['lang' => strtoupper($locale)]) }}">
							</div>
						</div>
					@endforeach
				</div>
			</div>


			<div class="row g-3 mt-2">
				<div class="col-md-12">
					<label for="page_slug" class="form-label">{{ __('Page Slug') }}</label>
					<input class="form-control page_slug" name="page_slug" id="page_slug" type="text"
					       value="{{ old('page_slug') }}" required>
				</div>
			</div>
			<div class="row g-3 mt-2">
				<div class="col-md-12">
					<label for="breadcrumb" class="form-label d-flex align-items-center gap-1">
						{{ __('Breadcrumb') }}
						<span class="text-muted small">({{ __('Optional') }})</span>
						
						<!-- Info Tooltip Icon -->
						<i class="fas fa-info-circle text-primary"
						   data-coreui-toggle="tooltip"
						   data-coreui-placement="top"
						   title="{{ __('If no image is uploaded, the default breadcrumb background from general settings will be used.') }}">
						</i>
					</label>
					
					<!-- Your existing Image Input -->
					<x-img :old="old('breadcrumb')" name="breadcrumb"/>
				</div>
			
			</div>

			
			{{-- Submit --}}
			<x-form.submit-button icon="check">
				{{ __('Create Page') }}
			</x-form.submit-button>
		</form>
	</div>
</div>

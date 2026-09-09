@php $isHome = $page->slug === '/'; @endphp
<div class="card border-0 shadow-sm mb-4">
	<div class="card-body">
		<form method="POST" action="{{ route('admin.page.site.update', $page->id) }}" enctype="multipart/form-data">
			@csrf
			@method('PUT')
			
			<div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap">
				<h2 class="h5 mb-0">{{ __('Page Elements') }}</h2>
				@if(!$page->is_home)
					<div class="d-flex flex-column flex-sm-row flex-wrap align-items-center justify-content-center text-center gap-3">
						<div class="form-check form-switch">
							<label class="form-check-label me-2" for="is_breadcrumb">{{ __('Page Breadcrumb') }}</label>
							<input class="form-check-input coevs-switch me-2" type="checkbox" value="1" name="is_breadcrumb"
							       id="is_breadcrumb" {{ old('is_breadcrumb', $page->is_breadcrumb) ? 'checked' : '' }}>
						</div>
						<div class="form-check form-switch">
							<label class="form-check-label me-2" for="is_active">{{ __('Page Status') }}</label>
							<input class="form-check-input coevs-switch me-2" type="checkbox" value="1" name="is_active" id="is_active" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
						</div>
					</div>
				@endif
			</div>
			
			<div class="sortable-list drop-here mt-2 mb-3" id="pageComponent">
				<span class="text-muted drop-text d-block text-center py-3">
				    <i class="fa fa-upload me-2"></i> {{ __('Drop Component Here') }}
				</span>
				@forelse($page->component_ids as $id)
					@php $component = $components->firstWhere('id', $id); @endphp
					@if($component)
						<div class="item" draggable="true" data-index="{{ $component->id }}" data-name="{{ strtolower($component->component_name) }}">
							<input type="hidden" name="component[]" value="{{ $component->id }}">
							<div class="details">
								<img src="{{ asset($component->component_icon) }}" alt="icon">
								<span class="text-capitalize">{{ $component->component_name }}</span>
							</div>
							<div class="d-flex align-items-center gap-2 ms-auto">
								@unless($component->is_protected)
									<a href="{{ route('admin.page.component.edit', $component->id) }}" target="_blank"
									   class="component-manage d-flex align-items-center modal-tooltip text-decoration-none"
									   title="{{ __('Manage Component') }}" data-coreui-toggle="tooltip">
										<i class="fa-solid fa-gear fa-fw text-secondary"></i>
									</a>
								@else
									<i class="fa-solid fa-lock me-1"></i> {{ __('Protected') }}
								@endif
								
								<span class="manage-drag d-flex align-items-center" title="{{ __('Remove from Page') }}" data-coreui-toggle="tooltip" role="button">
								    <span class="toggle-icon">
								        <i class="fa-solid fa-circle-minus text-danger fa-fw"></i>
								    </span>
								</span>
							</div>
						</div>
					@endif
				@empty
					<span class="text-muted drop-text">
                        <i class="fa fa-upload me-2"></i> {{ __('Drop Component Here') }}
                    </span>
				@endforelse
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
								       value="{{ old("page_title.$locale", $page->title[$locale] ?? '') }}"
								       placeholder="{{ __('Enter page title for :lang', ['lang' => strtoupper($locale)]) }}">
							</div>
						</div>
					@endforeach
				</div>
			</div>
			
			<div class="row g-3">
				<div class="col-md-12">
					<label for="slug" class="form-label d-flex align-items-center">
						{{ __('Page Slug') }}
						@if($page->is_protected)
							<small class="ms-2 text-muted d-flex align-items-center">
								<i class="fa-solid fa-lock me-1"></i> {{ __('Protected') }}
							</small>
						@endif
					</label>
					
					<input
						class="form-control {{ $page->is_protected ? 'bg-light text-muted' : '' }}"
						name="page_slug"
						id="{{ !$page->is_protected ? 'page_slug' : 'slug' }}"
						type="text"
						value="{{ old('page_slug', $page->slug) }}"
						{{ $page->is_protected ? 'readonly' : '' }}
						required
					>
				</div>
			
			</div>
			@if(!$page->is_home)
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
					<x-img :old="old('breadcrumb', $page->breadcrumb)" name="breadcrumb" ref="coevs-remove-img"/>
					</div>
				</div>
			@endif
			<x-form.submit-button icon="check">
				{{ __('Save Changes') }}
			</x-form.submit-button>
		
		</form>
	</div>
</div>
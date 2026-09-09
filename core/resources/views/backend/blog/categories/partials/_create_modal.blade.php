<div class="modal fade" id="create-category-modal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content shadow-sm rounded-3">
			<div class="modal-header">
				<h5 class="modal-title" id="createCategoryModalLabel">{{ __('Add New Category') }}</h5>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				<form action="{{ route('admin.blog.category.store') }}" method="POST" id="create-category-form">
					@csrf
					
					{{-- Language Tabs --}}
					<ul class="nav nav-tabs mb-3" role="tablist">
						@foreach($locales as $locale => $label)
							<li class="nav-item" role="presentation">
								<button class="nav-link {{ $loop->first ? 'active' : '' }}"
								        id="create-locale-tab-{{ $locale }}"
								        data-coreui-toggle="tab"
								        data-coreui-target="#create-locale-{{ $locale }}"
								        type="button" role="tab">
									{{ $label }}
								</button>
							</li>
						@endforeach
					</ul>
					
					{{-- Language Content --}}
					<div class="tab-content">
						@foreach($locales as $locale => $label)
							<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="create-locale-{{ $locale }}" role="tabpanel">
								<div class="mb-3">
									<label class="form-label">{{ __('Name') }} ({{ strtoupper($locale) }})</label>
									<input type="text"
									       name="name[{{ $locale }}]"
									       class="form-control {{ $locale == app()->getDefaultLocale() ? 'title-to-slug' : '' }}"
									       {{ $locale == app()->getDefaultLocale() ? 'data-slug-target=#create-category-slug' : '' }}
									       placeholder="{{ __('Enter category name') }}">
								</div>
							</div>
						@endforeach
					</div>
					
					{{-- Slug Field --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Slug (optional)') }}</label>
						<input type="text" name="slug" id="create-category-slug" class="form-control" placeholder="{{ __('Auto generated or you can type') }}">
					</div>
					
					{{-- Status Switch --}}
					<div class="mb-3">
						<label class="form-check-label" for="create-category-status">{{ __('Active') }}</label>
						<div class="form-check form-switch">
							<input class="form-check-input coevs-switch" type="checkbox" name="status" id="create-category-status" value="1" checked>
						</div>
					</div>
					
					{{-- Submit Button --}}
					<div class="text-end">
						<button type="submit" class="btn btn-primary">
							<x-icon name="check" height="20" width="20"/>
							{{ __('Save Now') }}
						</button>
					</div>
				
				</form>
			</div>
		</div>
	</div>
</div>

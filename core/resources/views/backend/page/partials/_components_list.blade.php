<div class="card border-0 shadow-sm mb-4">
	<div class="card-body">
		<div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap">
			<h2 class="h5 mb-0">{{ __('Component') }}</h2>
			
			<div class="input-group w-auto">
                <span class="input-group-text">
                    <x-icon name="search"/>
                </span>
				<input type="text" class="form-control rounded-end" id="componentSearch" placeholder="{{ __('Search Component') }}"/>
				<a href="{{ route('admin.page.component.index') }}" class="btn btn-sm btn-primary ms-2 rounded d-flex align-items-center">
					<x-icon name="component2" class="me-1"/>
					{{ __('Manage') }}
				</a>
			</div>
		</div>
		
		<div class="sortable-list mt-3" id="componentList">
			<button class="btn btn-info w-100 loading" type="button" disabled hidden>
				<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
				{{ __('Loading') }}...
			</button>
			
			@forelse($components as $component)
				@php
					$isSelected = isset($selected) && in_array($component->id, $selected);
				@endphp
				@if(!$isSelected)
					<div class="item" draggable="true"
					     data-index="{{ $component->id }}"
					     data-name="{{ strtolower($component->component_name) }}">
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
							
							<span class="manage-drag d-flex align-items-center text-decoration-underline" title="{{ __('Add to Page') }}" data-coreui-toggle="tooltip" role="button">
						    <span class="toggle-icon">
						        <i class="fa-solid fa-circle-plus text-success fa-fw"></i>
						    </span>
						</span>
						
						
						</div>
					</div>
				@endif
			@empty
				<h4 class="text-center text-muted h5 component-empty-text">{{ __('No Component Available') }}</h4>
			@endforelse
		</div>
	</div>
</div>

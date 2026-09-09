@extends('backend.layouts.app')
@section('title', __('Notifications Template'))
@section('content')
	<div class="clearfix my-3">
		<div class="fs-3 fw-semibold float-start"> {{ __('Notifications Template') }}</div>

		@can('notification-plugin-list')
			<a href="{{ route('admin.settings.plugin_type', 'notification') }}"
			   class="btn btn-primary float-end">
				<x-icon name="bell-3" height="24" width="24" class="me-2"/>
				{{ __('Notification Setting') }}
			</a>
		@endcan
		
	</div>
	
	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table align-middle">
					<thead class="table-light">
					<tr>
						<th>{{ __('Name') . ' | ' . __('Info') }}</th>
						<th>{{ __('Channels | Status') }}</th>
						<th>{{ __('Variables') }}</th>
						@can('notification-template-manage')
							<th>{{ __('Action') }}</th>
						@endcan
					</tr>
					</thead>
					<tbody>
					@foreach($notifyTemplates as $template)
						
						@php
							// collect variables and count
							$vars  = collect($template->variables);
							$count = $vars->count();
					
							if ($count > 3) {
								// split into two pretty-even chunks
								$half   = (int) ceil($count / 2);
								$first  = $vars->slice(0, $half);
								$second = $vars->slice($half);
							} else {
								// 3 or fewer: just one row
								$first  = $vars;
								$second = collect();
							}
						@endphp
						
						<tr>
							<td>
								<div class="d-flex align-items-center">
									<x-icon :name="$template->icon" height="35" width="35" class="me-2 text-{{ $template->action_type->class() }}"/>
									<div>
										<div class="fw-bold">
											{{ $template->name }}
											<span class="badge bg-{{ $template->user_type->color() }} text-uppercase">
			                                    <i class="fa-solid fa-{{ $template->user_type->icon() }} me-1"></i>
			                                    {{ $template->user_type->label() }}
			                                </span>
										</div>
										<div class="small text-muted">{{ $template->info }}</div>
									</div>
								</div>
							</td>
							<td>
								@foreach($template->channels as $channel)
									<div>
										<i class="{{ $channel->channel->icon() }} me-1"></i>{{ $channel->channel->label() }}
										<span class="badge badge-sm bg-{{ $channel->is_active ? 'success' : 'danger' }} text-uppercase"> {{ $channel->is_active ? __('Active') : __('Inactive') }} </span>
									</div>
								@endforeach
							</td>
							<td>
								
								<div class="variable-row">
									@foreach($first as $variable)
										<span class="variable-tag">{{ '{'.$variable.'}' }}</span>
									@endforeach
								</div>
								@if($second->isNotEmpty())
									<div class="variable-row">
										@foreach($second as $variable)
											<span class="variable-tag">{{ '{'.$variable.'}' }}</span>
										@endforeach
									</div>
								@endif
							</td>
							@can('notification-template-manage')
								<td>
									<a href="{{ route('admin.notifications.template.edit', $template->id) }}"
									   class="btn btn-primary">
										<x-icon name="manage" height="20" width="20"/>
										{{ __('Manage') }}
									</a>
								</td>
							@endcan
							
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			{{-- Pagination --}}
			<div class="d-flex justify-content-end mt-3">
				{{ $notifyTemplates->links() }}
			</div>
		</div>
	</div>
@endsection

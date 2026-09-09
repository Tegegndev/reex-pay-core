@extends('backend.layouts.app')
@section('title', __('Manage Notification Template'))
@section('content')
	
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h4 class="fw-semibold">{{ $template->name }}</h4>
		<a href="{{ route('admin.notifications.template.index') }}" class="btn btn-secondary">
			<x-icon name="back" class="me-1"/> {{ __('Back to list') }}
		</a>
	</div>
	<div class="card border-0 px-3 py-4">
	
		
		<ul class="nav nav-pills bg-light rounded p-1" id="channelTabs" role="tablist">
			@foreach($template->channels as $channel)
				<li class="nav-item" role="presentation">
					<button class="nav-link @if($loop->first) active @endif"
					        id="tab-{{ $channel->channel->value }}"
					        data-coreui-toggle="tab"
					        data-coreui-target="#pane-{{ $channel->channel->value }}"
					        type="button" role="tab">
						<i class="{{ $channel->channel->icon() }} me-1"></i>
						{{ $channel->channel->label() }} {{ __('Channel') }}
					</button>
				</li>
			@endforeach
		</ul>
		
		<div class="tab-content  p-4" id="channelTabContent">
			@foreach($template->channels as $channel)
				<div class="tab-pane fade @if($loop->first) show active @endif"
				     id="pane-{{ $channel->channel->value }}"
				     role="tabpanel">
					<form action="{{ route('admin.notifications.template.update', [$template->id, $channel->id]) }}" method="POST">
						@csrf
						@method('PUT')
						
						@unless($channel->channel == \App\Enums\NotificationChannelType::SMS)
							<div class="mb-3">
								<label class="form-label">{{ $channel->channel == \App\Enums\NotificationChannelType::EMAIL ? __('Email Subject') : __('Title') }}</label>
								<input type="text" name="title" class="form-control" value="{{ $channel->title }}">
							</div>
						@endunless
						<div class="mb-3">
							<label class="form-label">{{ __('Message Content') }}</label>
							<textarea name="message" class="form-control" rows="5">{{ $channel->message }}</textarea>
							<div class="form-text">
								{{ __('Available variables/shortcode :') }}
								@foreach($template->variables as $variable)
									<span class="badge variable-tag"> {{ '{$'.$variable.'}' }} </span>
								@endforeach
							</div>
						</div>
						<div class="col-md-3 mb-3 mt-2">
							<div class="card">
								<div class="form-check form-switch card-body p-2 border rounded d-flex align-items-center">
									<label class="form-check-label flex-grow-1" for="is_active">{{ __('Activate This Channel') }}</label>
									<input class="form-check-input coevs-switch me-2 flex-shrink-0" type="checkbox" role="switch" name="is_active" id="is_active" value="1" @checked($channel->is_active) >
								</div>
							</div>
						</div>
						
						<div class="text-end">
							<button type="submit" class="btn btn-primary">
								<x-icon name="check" class="me-1" height="20" width="20"/> {{ __('Update Now') }}
							</button>
						</div>
					</form>
				</div>
			@endforeach
		</div>
	</div>
@endsection

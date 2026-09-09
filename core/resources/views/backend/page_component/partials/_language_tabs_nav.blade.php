<ul class="nav nav-tabs" id="{{ $tabIdPrefix }}" role="tablist">
	@foreach($languages as $code => $lang)
		<li class="nav-item" role="presentation">
			<button class="nav-link @if($loop->first) active @endif"
			        id="{{ $tabIdPrefix }}-{{ $code }}"
			        data-coreui-toggle="tab"
			        data-coreui-target="#{{ $tabIdPrefix }}-pane-{{ $code }}"
			        type="button"
			        role="tab"
			        aria-controls="{{ $tabIdPrefix }}-pane-{{ $code }}"
			        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
				{{ $lang }}
			</button>
		</li>
	@endforeach
</ul>

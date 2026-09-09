<ul>
	@foreach($navigations as $navigation)
		<li>
			<a href="{{ $navigation->url }}" target="{{$navigation->target}}">{{ $navigation->label  }}</a>
		</li>
	@endforeach
</ul>
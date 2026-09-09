@props(['name', 'items', 'selected' => null, 'optional' => false, 'submit' => false])

<select class="form-select" aria-label="Default" name="{{ $name }}"
        @if($submit) onChange='submit();' @endif>
	@if($optional)
		<option value> -----</option>
	@endif
	@foreach($items as $key => $value)
		<option value="{{ is_numeric($key) ? $value : $key }}"
		        @if($selected === (is_numeric($key) ? $value : $key)) selected="selected" @endif>
			{{ ucfirst($value) }}
		</option>
	@endforeach
</select>

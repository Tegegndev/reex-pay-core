@if(isset($label) && $isLabel)
    <label for="{{ $name }}" class="form-label small">{{ title($label) }}</label>
@endif
<select name="{{ $name }}" id="{{ $name }}" class="form-select  pe-5 {{ $class ?? '' }}" {{ $attributes }}>
    <option value selected> {{ $label ??  __('All') }} </option>
    @foreach($options as $key => $value)
        <option value="{{ $key }}" {{ isset($selected) && $selected == $key ? 'selected' : '' }}>
            {{ is_callable($value) ? title($value($key)) : title($value) }}
        </option>
    @endforeach
</select>

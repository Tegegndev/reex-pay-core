@include('backend.settings.site.partials.fields._level')
<select class="form-select" aria-label="{{ $field['key'] }}" name="{{ $field['key'] }}" id="{{ $field['key'] }}">
    @foreach(setting_select_options($field['key']) as  $key => $value)
        <option @selected(setting($field['key'],$field['value']) == $value) value="{{ $value }}">{{ is_numeric($key) ? title($value) : $key  }}</option>
    @endforeach
</select>



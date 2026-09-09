@include('backend.settings.site.partials.fields._level')
<input type="text" name="{{ $field['key'] }}" class="form-control" id="{{ $field['key'] }}"
       value="{{ setting($field['key'],$field['value']) }}" placeholder="{{ title($field['label']) }}">

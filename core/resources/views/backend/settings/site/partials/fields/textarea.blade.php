@include('backend.settings.site.partials.fields._level')
<textarea class="form-control" name="{{ $field['key'] }}" id="{{ $field['key'] }}"  rows="6">{{ setting($field['key'],$field['value']) }}</textarea>

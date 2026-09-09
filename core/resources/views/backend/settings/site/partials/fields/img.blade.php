@include('backend.settings.site.partials.fields._level')
<x-img name="{{ $field['key'] }}" :old="setting($field['key'],$field['value']) ?? 'img/placeholder.png'"/>

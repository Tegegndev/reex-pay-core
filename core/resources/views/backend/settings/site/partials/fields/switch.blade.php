@include('backend.settings.site.partials.fields._level')
<div class="form-check form-switch">
    <input type="hidden" name="{{ $field['key'] }}" value="0">
    <input class="form-check-input coevs-switch" type="checkbox" role="switch" name="{{ $field['key'] }}" value="1" @checked(setting($field['key'],$field['value']))>
</div>

<div class="avatar-upload">
    <div class="avatar-edit">
	    <input type='file' class="imageUpload" name="{{ $name }}" id="{{ $id }}_upload" data-preview-id="{{ $id }}" accept=".png, .jpg, .jpeg"/>
	    <label for="{{ $id }}_upload" class="imageUpload">
            <x-icon name="upload"/>
        </label>
        @if($ref === 'coevs-remove-img')
		    <input type='hidden' class="imageUpload" id="{{ $id }}-remove" data-preview-id="{{ $id }}"/>
		    <label for="{{ $id }}-remove" class="imageRemove">
                <x-icon name="close"/>
            </label>
        @endif
    </div>
    <div class="avatar-preview">
	    <div id="{{ $id }}" style="background-image: url('{{ $old }}')"></div>
    </div>
</div>

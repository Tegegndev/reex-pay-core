<div class="d-flex justify-content-end mt-4">
    <button type="{{ $type }}" class="btn {{ $color }}" {{ $attributes }}>
        @isset($icon)
            <x-icon :name="$icon" class="me-1" height="20" width="20" />
        @endisset
        {{ $slot }}
    </button>
</div>

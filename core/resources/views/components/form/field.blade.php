<div class="{{ $colClass }}">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @if ($type === 'text' || $type === 'file')
        <input
                type="{{ $type }}"
                name="{{ $name }}"
                class="form-control @error($name) is-invalid @enderror"
                id="{{ $name }}"
                placeholder="{{ $placeholder }}"
                value="{{ $type === 'text' ? old($name, $value) : '' }}"
                {{ $required ? 'required' : '' }}>
    @elseif ($type === 'textarea')
        <textarea
                name="{{ $name }}"
                class="form-control @error($name) is-invalid @enderror"
                id="{{ $name }}"
                placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
    >{{ old($name, $value) }}</textarea>
    @endif

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

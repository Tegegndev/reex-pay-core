@foreach($translations as $type => $items)
    @foreach($items as $group => $translations)
        @foreach($translations as $key => $value)
            @php
                $defaultLanguageValue = $value[config('app.locale')];
                $translatedValue = $value[$languageCode];
            @endphp

            @if(!is_array($defaultLanguageValue))
                <tr>
                    <td>{{ $group }}</td>
                    <td>{{ Str::limit($key, 30) }}</td>
                    <td>{{ Str::limit($defaultLanguageValue, 30) }}</td>
                    <td>{{ Str::limit($translatedValue, 30) }}</td>
                    <td>
                        <a href="javascript:void(0)" class="editKeyword"
                           data-language="{{ $languageCode }}"
                           data-group="{{ $group }}" data-key="{{ $key }}"
                           data-value="{{ $translatedValue }}"
                           data-coreui-toggle="tooltip" title="Edit Keyword">
                            <x-icon name="edit" height="30" width="30"/>
                        </a>
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
@endforeach


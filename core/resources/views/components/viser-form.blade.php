@foreach($formData ?? [] as $data)
        @if($data->type == 'text')
                <div class="mb-6">
                    <label for="trx_id" class="block mb-2 text-xl font-bold text-white">{{ __($data->name) }}</label>
                    <input type="text" id="{{ $data->label }}" name="{{ $data->label }}" placeholder={{ __($data->name) }}"
                        class="bg-pink bg-111 text-gray-100 text-xl rounded-lg focus:ring-0 focus:border-0 border-0 placeholder:text-gray-100 block w-full p-2.5"
                        @if($data->is_required == 'required') required @endif />
        @elseif($data->type == 'textarea')
            <textarea
                class="form-control form--control"
                name="{{ $data->label }}"
                @if($data->is_required == 'required') required @endif
            >{{ old($data->label) }}</textarea>
        @elseif($data->type == 'select')
            <select
                class="form-control form--control"
                name="{{ $data->label }}"
                @if($data->is_required == 'required') required @endif
            >
                <option value="">@lang('Select One')</option>
                @foreach (manualFieldOptions($data) as $item)
                    <option value="{{ $item }}" @selected($item == old($data->label))>{{ __($item) }}</option>
                @endforeach
            </select>
        @elseif($data->type == 'checkbox')
            @foreach(manualFieldOptions($data) as $option)
                <div class="form-check">
                    <input
                        class="form-check-input"
                        name="{{ $data->label }}[]"
                        type="checkbox"
                        value="{{ $option }}"
                        id="{{ $data->label }}_{{ titleToKey($option) }}"
                    >
                    <label class="form-check-label" for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                </div>
            @endforeach
        @elseif($data->type == 'radio')
            @foreach(manualFieldOptions($data) as $option)
                <div class="form-check">
                    <input
                    class="form-check-input"
                    name="{{ $data->label }}"
                    type="radio"
                    value="{{ $option }}"
                    id="{{ $data->label }}_{{ titleToKey($option) }}"
                    @checked($option == old($data->label))
                    >
                    <label class="form-check-label" for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                </div>
            @endforeach
        @elseif($data->type == 'file')
            <input
            type="file"
            class="form-control form--control"
            name="{{ $data->label }}"
            @if($data->is_required == 'required') required @endif
            accept="@foreach(explode(',', manualFieldExtensions($data)) as $ext) .{{ trim($ext) }}, @endforeach"
            >
            <pre class="text--base mt-1">@lang('Supported mimes'): {{ $data->extensions }}</pre>
        @endif
    </div>
@endforeach

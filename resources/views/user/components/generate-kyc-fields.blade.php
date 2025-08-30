@if (isset($fields) && count($fields) > 0)
    @foreach ($kyc_fields as $item)
        @if ($item->type == 'select')
            <div class="col-xl-3 col-lg-3 col-md-12 mb-20">
                <label class="title" for="{{ $item->name }}">{{ $item->label }}<span>@if ($item->validation->required)*@endif</span></label>
                <select name="{{ $item->name }}" id="{{ $item->name }}" class="nice-select">
                    <option selected disabled>Choose One</option>
                    @foreach ($item->validation->options as $innerItem)
                        <option value="{{ $innerItem }}">{{ $innerItem }}</option>
                    @endforeach
                </select>
                @error($item->name)
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        @elseif ($item->type == 'file')
            <div class="col-xl-4 col-lg-4 col-md-6 mb-20 form-group">
                <label>{{ __($item->label) }}<span>@if ($item->validation->required)*@endif</span></label>
                <div class="file-holder-wrapper">
                    <input type="{{ $item->type }}" class="file-holder" name="{{ $item->name }}" id="fileUpload" data-height="130" accept="image/*"
                        data-max_size="20" data-file_limit="15" value="{{ old($item->name) }}">
                </div>
            </div>
        @elseif ($item->type == 'text')
            <div class="col-lg-12 form-group">
                @include('admin.components.form.input', [
                    'label' => $item->label,
                    'name' => $item->name,
                    'type' => $item->type,
                    'value' => old($item->name),
                ])
            </div>
        @elseif ($item->type == 'textarea')
            <div class="col-lg-12 form-group">
                @include('admin.components.form.textarea', [
                    'label' => $item->label,
                    'name' => $item->name,
                    'value' => old($item->name),
                ])
            </div>
        @endif
    @endforeach
@endisset

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('Admin Name')</label>
            <input type="text" name="name" class="form-control" value="{{ !empty($remember) ? old('name') : (isset($admin) ? $admin->name : '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('Category / Department')</label>
            <input type="text" name="category" class="form-control" value="{{ !empty($remember) ? old('category') : (isset($admin) ? $admin->category : '') }}" placeholder="@lang('Working Related Issues')" required>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>@lang('Short Instructions / Purpose')</label>
            <textarea name="instructions" class="form-control" rows="2" placeholder="@lang('What should the user contact this admin for?')">{{ !empty($remember) ? old('instructions') : (isset($admin) ? $admin->instructions : '') }}</textarea>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Contact Number')</label>
            <input type="text" name="contact_number" class="form-control" value="{{ !empty($remember) ? old('contact_number') : (isset($admin) ? $admin->contact_number : '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('WhatsApp Number')</label>
            <input type="text" name="whatsapp_number" class="form-control" value="{{ !empty($remember) ? old('whatsapp_number') : (isset($admin) ? $admin->whatsapp_number : '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Contact Type')</label>
            <select name="contact_type" class="form-control" required>
                @foreach(['whatsapp' => 'WhatsApp', 'call' => 'Call', 'both' => 'WhatsApp and Call'] as $value => $label)
                    <option value="{{ $value }}" @selected((!empty($remember) ? old('contact_type', 'whatsapp') : (isset($admin) ? $admin->contact_type : 'whatsapp')) === $value)>{{ __($label) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

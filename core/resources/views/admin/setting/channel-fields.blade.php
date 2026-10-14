@php
    $channel = $channel ?? null;
    $remember = !empty($remember);
    $field = function ($name, $fallback = '') use ($channel, $remember) {
        if ($remember) {
            return old($name, $channel->{$name} ?? $fallback);
        }
        return $channel->{$name} ?? $fallback;
    };
@endphp
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('Channel Title')</label>
            <input type="text" name="title" class="form-control" maxlength="150" required value="{{ $field('title') }}" placeholder="@lang('Flowers Official Channel')">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('Channel Purpose')</label>
            <input type="text" name="purpose" class="form-control" maxlength="255" value="{{ $field('purpose') }}" placeholder="@lang('Trainings | Guidelines | Earning Tips')">
            <small class="text-muted">@lang('Short line shown on the channel card.')</small>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>@lang('Description')</label>
            <textarea name="description" class="form-control" rows="3" maxlength="1000" placeholder="@lang('What members will find in this channel')">{{ $field('description') }}</textarea>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label>@lang('Channel Link')</label>
            <input type="url" name="link" class="form-control" maxlength="500" required value="{{ $field('link') }}" placeholder="https://whatsapp.com/channel/...">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Optional Icon')</label>
            <input type="text" name="icon" class="form-control" maxlength="80" list="channel-icon-presets" value="{{ $field('icon', 'fab fa-whatsapp') }}" placeholder="fab fa-whatsapp">
            <datalist id="channel-icon-presets">
                <option value="fab fa-whatsapp"></option>
                <option value="fas fa-broadcast-tower"></option>
                <option value="fas fa-bullhorn"></option>
                <option value="fas fa-gift"></option>
                <option value="fas fa-graduation-cap"></option>
                <option value="fas fa-users"></option>
            </datalist>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Sort Order')</label>
            <input type="number" name="sort_order" min="0" class="form-control" value="{{ $field('sort_order') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Status')</label>
            @php
                $statusValue = (string) (int) ($remember ? old('status', isset($channel) ? (int) $channel->status : 1) : (isset($channel) ? (int) $channel->status : 1));
            @endphp
            <select name="status" class="form-control">
                <option value="1" @selected($statusValue === '1')>@lang('Enabled')</option>
                <option value="0" @selected($statusValue === '0')>@lang('Disabled')</option>
            </select>
        </div>
    </div>
</div>

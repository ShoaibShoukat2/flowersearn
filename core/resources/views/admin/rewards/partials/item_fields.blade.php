@php $prefix = $prefix ?? ''; @endphp
<div class="form-group">
    <label>@lang('Title')</label>
    <input type="text" name="title" class="form-control" required>
</div>
<div class="form-group">
    <label>@lang('Description')</label>
    <textarea name="description" rows="3" class="form-control"></textarea>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Icon')</label>
            <input type="text" name="icon" class="form-control" placeholder="🎁 or fas fa-gift">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Badge')</label>
            <input type="text" name="badge" class="form-control" placeholder="Rs 50">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>@lang('Sort Order')</label>
            <input type="number" name="sort_order" class="form-control" value="0" min="0">
        </div>
    </div>
</div>
<div class="form-group">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="status" id="{{ $prefix }}reward_status" value="1" checked>
        <label class="form-check-label" for="{{ $prefix }}reward_status">@lang('Active')</label>
    </div>
</div>

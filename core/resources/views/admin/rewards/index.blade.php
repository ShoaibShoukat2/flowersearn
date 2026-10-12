@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header bg--primary">
                <h5 class="text-white mb-0">@lang('Rewards Page Settings')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rewards.settings') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Page Title')</label>
                                <input type="text" name="page_title" class="form-control" value="{{ old('page_title', $settings->page_title) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Page Subtitle')</label>
                                <input type="text" name="page_subtitle" class="form-control" value="{{ old('page_subtitle', $settings->page_subtitle) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Tips Section Title')</label>
                                <input type="text" name="tips_title" class="form-control" value="{{ old('tips_title', $settings->tips_title) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="d-block">@lang('Display Options')</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="show_levels" id="show_levels" value="1" {{ $settings->show_levels ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_levels">@lang('Show Levels Progress')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="show_on_dashboard" id="show_on_dashboard" value="1" {{ $settings->show_on_dashboard ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_on_dashboard">@lang('Show on User Dashboard')</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Tips / Extra Content')</label>
                                <textarea name="tips_content" rows="5" class="form-control">{{ old('tips_content', $settings->tips_content) }}</textarea>
                                <small class="text-muted">@lang('One point per line. This shows on the Rewards page for users.')</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Page Settings')</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg--primary d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">@lang('Reward Items')</h5>
                <button type="button" class="btn btn-sm btn--light" data-bs-toggle="modal" data-bs-target="#addRewardModal">
                    <i class="las la-plus"></i> @lang('Add Reward')
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--sm">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Icon')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Description')</th>
                                <th>@lang('Badge')</th>
                                <th>@lang('Order')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td data-label="@lang('Icon')"><span style="font-size:1.4rem;">{{ $item->icon ?: '🎁' }}</span></td>
                                <td data-label="@lang('Title')">{{ $item->title }}</td>
                                <td data-label="@lang('Description')">{{ Str::limit($item->description, 80) }}</td>
                                <td data-label="@lang('Badge')">{{ $item->badge ?: '-' }}</td>
                                <td data-label="@lang('Order')">{{ $item->sort_order }}</td>
                                <td data-label="@lang('Status')">
                                    @if($item->status)
                                        <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Inactive')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <button type="button"
                                        class="btn btn-sm btn-outline--primary editRewardBtn"
                                        data-id="{{ $item->id }}"
                                        data-title="{{ $item->title }}"
                                        data-description="{{ $item->description }}"
                                        data-icon="{{ $item->icon }}"
                                        data-badge="{{ $item->badge }}"
                                        data-sort_order="{{ $item->sort_order }}"
                                        data-status="{{ $item->status ? 1 : 0 }}">
                                        <i class="la la-pencil"></i>
                                    </button>
                                    <a href="{{ route('admin.rewards.item.toggle', $item->id) }}" class="btn btn-sm btn-outline--info">
                                        <i class="las la-toggle-on"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                        data-action="{{ route('admin.rewards.item.delete', $item->id) }}"
                                        data-question="@lang('Are you sure to delete this reward item?')">
                                        <i class="la la-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">@lang('No reward items found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add Reward Item')</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <form action="{{ route('admin.rewards.item.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin.rewards.partials.item_fields')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Save Reward')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Reward Item')</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <form action="" method="POST" id="editRewardForm">
                @csrf
                <div class="modal-body">
                    @include('admin.rewards.partials.item_fields', ['prefix' => 'edit_'])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Update Reward')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('script')
<script>
(function ($) {
    "use strict";

    $('.editRewardBtn').on('click', function () {
        const modal = $('#editRewardModal');
        const form = $('#editRewardForm');
        const data = $(this).data();

        form.attr('action', `{{ url('admin/rewards/item') }}/${data.id}`);
        modal.find('[name="title"]').val(data.title);
        modal.find('[name="description"]').val(data.description);
        modal.find('[name="icon"]').val(data.icon);
        modal.find('[name="badge"]').val(data.badge);
        modal.find('[name="sort_order"]').val(data.sort_order);
        modal.find('[name="status"]').prop('checked', Number(data.status) === 1);
        modal.modal('show');
    });
})(jQuery);
</script>
@endpush

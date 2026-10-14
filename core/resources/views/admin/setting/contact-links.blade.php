@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Add Channel')</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">@lang('Add as many official channels as you need. Enabled channels appear on the user dashboard.')</p>
                    <form action="{{ route('admin.setting.channels.store') }}" method="POST">
                        @csrf
                        @include('admin.setting.channel-fields', ['remember' => true])
                        <button type="submit" class="btn btn--primary">@lang('Add Channel')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        @forelse($channels as $channel)
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2">
                        <h5 class="card-title mb-0">{{ $channel->title }}</h5>
                        <span class="badge badge--{{ $channel->status ? 'success' : 'warning' }}">{{ $channel->status ? __('Enabled') : __('Disabled') }}</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.setting.channels.update', $channel->id) }}" method="POST">
                            @csrf
                            @include('admin.setting.channel-fields', ['channel' => $channel])
                            <button type="submit" class="btn btn--primary">@lang('Update Channel')</button>
                        </form>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <form action="{{ route('admin.setting.channels.status', $channel->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline--{{ $channel->status ? 'warning' : 'success' }}">
                                    {{ $channel->status ? __('Disable Channel') : __('Enable Channel') }}
                                </button>
                            </form>
                            <form action="{{ route('admin.setting.channels.delete', $channel->id) }}" method="POST" onsubmit="return confirm('Remove this channel?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Remove Channel')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted">@lang('No channels yet. Add the first one above.')</div>
                </div>
            </div>
        @endforelse
    </div>
@endsection

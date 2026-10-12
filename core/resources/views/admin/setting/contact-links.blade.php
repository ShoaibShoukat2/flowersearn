@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4">
                        @lang('Update WhatsApp Community and Channel links shown on Official Channels, the pending review screen, and the sidebar.')
                    </p>
                    <form action="{{ route('admin.setting.contact.links.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>@lang('WhatsApp Community Link')</label>
                                    <input class="form-control" type="url" name="whatsapp_community_link" placeholder="https://chat.whatsapp.com/..." value="{{ $general->whatsapp_community_link }}">
                                    <small class="text-muted">@lang('Official community group link (e.g. chat.whatsapp.com/...)')</small>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>@lang('WhatsApp Channel Link')</label>
                                    <input class="form-control" type="url" name="whatsapp_channel_link" placeholder="https://whatsapp.com/channel/..." value="{{ $general->whatsapp_channel_link }}">
                                    <small class="text-muted">@lang('Official channel link for announcements and updates.')</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Links')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Add Admin')</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">@lang('These admins appear on the user dashboard Contact Us page. Add as many as you need.')</p>
                    <form action="{{ route('admin.setting.contact.admins.store') }}" method="POST">
                        @csrf
                        @include('admin.setting.contact-admin-fields', ['remember' => true])
                        <button type="submit" class="btn btn--primary">@lang('Add Admin')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        @forelse($admins as $admin)
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $admin->name }}</h5>
                        <form action="{{ route('admin.setting.contact.admins.delete', $admin->id) }}" method="POST" onsubmit="return confirm('Remove this admin from Contact Us?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Delete')</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.setting.contact.admins.update', $admin->id) }}" method="POST">
                            @csrf
                            @include('admin.setting.contact-admin-fields', ['admin' => $admin])
                            <button type="submit" class="btn btn--primary">@lang('Update')</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted">@lang('No contact admins yet. Add the first one above.')</div>
                </div>
            </div>
        @endforelse
    </div>
@endsection

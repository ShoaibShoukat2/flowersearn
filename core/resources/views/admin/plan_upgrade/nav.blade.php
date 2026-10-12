@php
    $pendingUpgradeBadge = 0;
    try {
        if (ensurePlanUpgradesTable()) {
            $pendingUpgradeBadge = \App\Models\PlanUpgrade::pending()->count();
        }
    } catch (\Throwable $e) {
        $pendingUpgradeBadge = 0;
    }
@endphp
<ul class="nav nav-pills mb-3 flex-wrap gap-2">
    <li class="nav-item">
        <a class="nav-link {{ menuActive('admin.plan.upgrade.dashboard') }}" href="{{ route('admin.plan.upgrade.dashboard') }}">@lang('Dashboard')</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuActive('admin.plan.upgrade.pending') }}" href="{{ route('admin.plan.upgrade.pending') }}">
            @lang('Pending')
            @if($pendingUpgradeBadge)
                <span class="badge bg--danger">{{ $pendingUpgradeBadge }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuActive('admin.plan.upgrade.approved') }}" href="{{ route('admin.plan.upgrade.approved') }}">@lang('Approved')</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuActive('admin.plan.upgrade.rejected') }}" href="{{ route('admin.plan.upgrade.rejected') }}">@lang('Rejected')</a>
    </li>
</ul>

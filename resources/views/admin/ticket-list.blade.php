@extends('layouts.admin')
@section('page-title', $listingTitle)
@section('content')
<div class="admin-ticket-page">
    <div class="admin-page-head"><div><h1 class="admin-page-title">{{ $listingTitle }}</h1><p class="admin-page-subtitle">Find attendees, review receipts, and manage admission.</p></div><span class="admin-count-pill"><i class="bi bi-ticket-perforated" aria-hidden="true"></i>{{ number_format($tickets->total()) }} {{ Str::plural('result', $tickets->total()) }}</span></div>
    <nav class="ticket-status-tabs" aria-label="Ticket status filters">
        @foreach ([null => ['All tickets', route('admin.tickets.index'), $statusCounts->sum()], 'pending' => ['Pending', route('admin.tickets.pending'), $statusCounts['pending'] ?? 0], 'verified' => ['Verified', route('admin.tickets.index', ['status' => 'verified']), ($statusCounts['verified'] ?? 0) + ($statusCounts['checked_in'] ?? 0)], 'rejected' => ['Rejected', route('admin.tickets.rejected'), $statusCounts['rejected'] ?? 0], 'checked_in' => ['Checked in', route('admin.tickets.index', ['status' => 'checked_in']), $statusCounts['checked_in'] ?? 0]] as $status => $tab)
            <a href="{{ $tab[1] }}" class="ticket-status-tab {{ ($activeStatus ?? '') === $status ? 'is-active' : '' }}" @if (($activeStatus ?? '') === $status) aria-current="page" @endif>{{ $tab[0] }}<span>{{ $tab[2] }}</span></a>
        @endforeach
    </nav>
    <section class="admin-card ticket-directory">
        <form method="GET" action="{{ url()->current() }}" class="ticket-directory-filters" aria-label="Search tickets">
            @if (!$fixedStatus && $activeStatus)<input type="hidden" name="status" value="{{ $activeStatus }}">@endif
            @if (request('email'))<input type="hidden" name="email" value="{{ request('email') }}">@endif
            <div class="ticket-search-field"><label class="admin-form-label" for="ticket-search">Find a ticket</label><input class="admin-input" id="ticket-search" name="q" value="{{ request('q') }}" placeholder="Name, email, ticket ID, student ID or phone" type="search" maxlength="255"></div>
            <div><label class="admin-form-label" for="ticket-sort">Sort by</label><select class="admin-select" id="ticket-sort" name="sort"><option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option><option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option><option value="name" @selected(request('sort') === 'name')>Name A–Z</option></select></div>
            <div><label class="admin-form-label" for="ticket-page-size">Rows</label><select class="admin-select" id="ticket-page-size" name="per_page">@foreach ([15, 30, 50] as $size)<option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }}</option>@endforeach</select></div>
            <button class="admin-btn" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Search</button>
            @if (request()->hasAny(['q', 'email', 'sort', 'per_page']))<a class="admin-btn admin-btn-outline" href="{{ $fixedStatus ? url()->current() : route('admin.tickets.index', $activeStatus ? ['status' => $activeStatus] : []) }}">Reset</a>@endif
        </form>
        @if (request('email'))<p class="ticket-filter-note">Filtered by email: {{ request('email') }}</p>@endif
        @include('admin.partials.tickets-table', ['tickets' => $tickets])
    </section>
</div>
@push('scripts')<script src="{{ asset('admin/js/bulk-tickets.js') }}"></script>@endpush
@endsection

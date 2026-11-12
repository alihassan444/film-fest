@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
    @php
        $safeTotal = max($totalTickets, 1);
        $pendingWidth = $totalTickets ? ($pendingTickets / $safeTotal) * 100 : 0;
        $verifiedWidth = $totalTickets ? ($awaitingEntryTickets / $safeTotal) * 100 : 0;
        $rejectedWidth = $totalTickets ? ($rejectedTickets / $safeTotal) * 100 : 0;
        $checkedInWidth = $totalTickets ? ($checkedInTickets / $safeTotal) * 100 : 0;
    @endphp

    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-title">Admin Dashboard</h1>
            <p class="admin-page-subtitle">A clear view of your festival, all in one place.</p>
        </div>
    </div>

    <section class="dashboard-hero">
        <div class="dashboard-hero-copy">
            <div class="dashboard-eyebrow">Behind every great festival</div>
            <h2>Welcome back, {{ Auth::user()->name ?? 'Administrator' }}.</h2>
            <p>You have {{ $pendingTickets }} {{ Str::plural('pending ticket', $pendingTickets) }} ready for review.</p>
            <a class="dashboard-hero-link" href="{{ route('admin.tickets.pending') }}">
                Review pending tickets <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="dashboard-cinema-art" aria-hidden="true">
            <img src="{{ asset('admin/images/festival-cinema.svg') }}" alt="" width="300" height="210">
        </div>
    </section>

    <section class="dashboard-stats" aria-label="Ticket statistics">
        <article class="dashboard-stat">
            <div class="dashboard-stat-head"><span>Total tickets</span><span class="dashboard-stat-icon total"><i class="bi bi-ticket-perforated"></i></span></div>
            <div class="dashboard-stat-value">{{ $totalTickets }}</div>
            <div class="dashboard-stat-note">Unique submitted tickets</div>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-head"><span>Pending tickets</span><span class="dashboard-stat-icon pending"><i class="bi bi-clock"></i></span></div>
            <div class="dashboard-stat-value">{{ $pendingTickets }}</div>
            <div class="dashboard-stat-note">Awaiting your review</div>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-head"><span>Verified tickets</span><span class="dashboard-stat-icon verified"><i class="bi bi-patch-check"></i></span></div>
            <div class="dashboard-stat-value">{{ $verifiedTickets }}</div>
            <a class="dashboard-stat-note dashboard-insight-link" href="{{ route('admin.tickets.index', ['status' => 'verified']) }}">Includes checked-in attendees <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-head"><span>Rejected tickets</span><span class="dashboard-stat-icon rejected"><i class="bi bi-x-circle"></i></span></div>
            <div class="dashboard-stat-value">{{ $rejectedTickets }}</div>
            <a class="dashboard-stat-note dashboard-insight-link" href="{{ route('admin.tickets.rejected') }}">View rejected requests <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-head"><span>Checked in</span><span class="dashboard-stat-icon checked-in"><i class="bi bi-box-arrow-in-right"></i></span></div>
            <div class="dashboard-stat-value">{{ $checkedInTickets }}</div>
            <a class="dashboard-stat-note dashboard-insight-link" href="{{ route('admin.tickets.index', ['status' => 'checked_in']) }}">Of the verified tickets <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="admin-card">
            <div class="admin-card-header">
                <div><h2 class="admin-card-title">Quick actions</h2><p class="admin-card-copy">Go straight to what needs your attention.</p></div>
            </div>
            <div class="dashboard-actions">
                <a class="dashboard-action" href="{{ route('admin.tickets.index') }}"><span class="dashboard-action-label"><i class="bi bi-ticket-perforated"></i>View all tickets</span><i class="bi bi-arrow-up-right"></i></a>
                <a class="dashboard-action" href="{{ route('admin.tickets.pending') }}"><span class="dashboard-action-label"><i class="bi bi-clock"></i>View pending tickets</span><i class="bi bi-arrow-up-right"></i></a>
                <a class="dashboard-action" href="{{ route('admin.tickets.index', ['status' => 'verified']) }}"><span class="dashboard-action-label"><i class="bi bi-patch-check"></i>View verified tickets</span><i class="bi bi-arrow-up-right"></i></a>
                <a class="dashboard-action" href="{{ route('admin.tickets.rejected') }}"><span class="dashboard-action-label"><i class="bi bi-x-circle"></i>View rejected tickets</span><i class="bi bi-arrow-up-right"></i></a>
                <a class="dashboard-action" href="{{ route('admin.tickets.index', ['status' => 'checked_in']) }}"><span class="dashboard-action-label"><i class="bi bi-box-arrow-in-right"></i>View checked-in attendees</span><i class="bi bi-arrow-up-right"></i></a>
                <a class="dashboard-action" href="{{ route('admin.attendance') }}"><span class="dashboard-action-label"><i class="bi bi-journal-check"></i>Attendance & notes</span><i class="bi bi-arrow-up-right"></i></a>
                <a class="dashboard-action" href="{{ route('admin.students.index') }}"><span class="dashboard-action-label"><i class="bi bi-people"></i>Manage students</span><i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div><h2 class="admin-card-title">Ticket status</h2><p class="admin-card-copy">{{ $totalTickets }} {{ Str::plural('ticket', $totalTickets) }} across all statuses</p></div>
            </div>
            <div class="dashboard-status-body">
                <div class="dashboard-status-bar" aria-label="Ticket status distribution">
                    @if($totalTickets)
                        @if($pendingTickets)<span class="dashboard-status-segment" style="background:#569992;width:{{ $pendingWidth }}%"></span>@endif
                        @if($awaitingEntryTickets)<span class="dashboard-status-segment" style="background:#67917f;width:{{ $verifiedWidth }}%"></span>@endif
                        @if($rejectedTickets)<span class="dashboard-status-segment" style="background:#ae5b60;width:{{ $rejectedWidth }}%"></span>@endif
                        @if($checkedInTickets)<span class="dashboard-status-segment" style="background:#37537e;width:{{ $checkedInWidth }}%"></span>@endif
                    @else
                        <span class="dashboard-status-empty"></span>
                    @endif
                </div>
                <div class="dashboard-status-row"><i class="dashboard-status-dot" style="background:#569992"></i><span>Pending</span><strong>{{ $pendingTickets }}</strong></div>
                <div class="dashboard-status-row"><i class="dashboard-status-dot" style="background:#3e7e69"></i><span>Verified, awaiting entry</span><strong>{{ $awaitingEntryTickets }}</strong></div>
                <div class="dashboard-status-row"><i class="dashboard-status-dot" style="background:#ae5b60"></i><span>Rejected</span><strong>{{ $rejectedTickets }}</strong></div>
                <div class="dashboard-status-row"><i class="dashboard-status-dot" style="background:#37537e"></i><span>Checked in</span><strong>{{ $checkedInTickets }}</strong></div>
                <div class="dashboard-admission-insight"><div><span>Check-in rate</span><strong>{{ $checkInRate }}%</strong></div><p>{{ $checkedInTickets }} of {{ $approvedTickets }} verified tickets checked in. Check-ins are included in verified tickets and are not added to the total again.</p><progress max="100" value="{{ $checkInRate }}" aria-label="Approved ticket check-in rate">{{ $checkInRate }}%</progress></div>
                <div class="dashboard-student-total"><i class="bi bi-people"></i><span>Registered students</span><strong>{{ $studentCount }}</strong></div>
            </div>
        </div>
    </section>
@endsection

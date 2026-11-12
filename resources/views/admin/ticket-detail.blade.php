@extends('layouts.admin')
@section('page-title', 'Ticket details')
@push('styles')
    <link href="{{ asset('admin/css/ticket-detail.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="ticket-workspace">
    <div class="admin-page-head">
        <div><h1 class="admin-page-title">Ticket details</h1><p class="admin-page-subtitle">Review the attendee, payment receipt, and ticket activity.</p></div>
        <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn-outline"><i class="bi bi-arrow-left" aria-hidden="true"></i> All tickets</a>
    </div>
    <section class="ticket-summary" aria-label="Ticket summary">
        <div class="ticket-summary-identity"><span class="ticket-summary-icon" aria-hidden="true"><i class="bi bi-ticket-perforated"></i></span><div><span class="ticket-summary-label">Film Fest-26 / Ticket reference</span><h2>{{ $ticket->ticket_id }}</h2><p>Submitted {{ $ticket->created_at->format('d M Y, g:i A') }}</p></div></div>
        <span class="status-badge status-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
    </section>
    <div class="ticket-detail-grid">
        <div class="ticket-detail-main">
            <section class="admin-card">
                <div class="admin-card-header"><div class="d-flex align-items-center gap-3"><span class="admin-icon-tile"><i class="bi bi-person" aria-hidden="true"></i></span><div><h2 class="admin-card-title">Attendee information</h2><p class="admin-card-copy">Details provided at the time of booking.</p></div></div></div>
                <dl class="attendee-details">
                    @foreach (['Name' => $ticket->name, 'Email address' => $ticket->email, 'Phone number' => $ticket->phone, 'Student ID' => $ticket->student_id, 'Department' => $ticket->department, 'Semester' => $ticket->semester, 'CNIC' => $ticket->cnic] as $label => $value)
                        <div><dt>{{ $label }}</dt><dd>{{ $value ?: 'Not provided' }}</dd></div>
                    @endforeach
                </dl>
            </section>
            <section class="admin-card ticket-receipt-card">
                <div class="admin-card-header"><div><h2 class="admin-card-title">Payment receipt</h2><p class="admin-card-copy">Review the uploaded proof of payment.</p></div>
                    @if ($ticket->transaction_screenshot && file_exists(public_path($ticket->transaction_screenshot)))
                        <button type="button" class="admin-btn admin-btn-outline" data-bs-toggle="modal" data-bs-target="#receiptPreviewModal" data-receipt-src="{{ asset($ticket->transaction_screenshot) }}" data-ticket-reference="{{ $ticket->ticket_id }}">Open receipt <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i><span class="visually-hidden"> in a popup</span></button>
                    @endif
                </div>
                <div class="ticket-receipt-preview">
                    @if ($ticket->transaction_screenshot && file_exists(public_path($ticket->transaction_screenshot)))
                        <button type="button" data-bs-toggle="modal" data-bs-target="#receiptPreviewModal" data-receipt-src="{{ asset($ticket->transaction_screenshot) }}" data-ticket-reference="{{ $ticket->ticket_id }}"><img src="{{ asset($ticket->transaction_screenshot) }}" alt="Payment receipt for ticket {{ $ticket->ticket_id }}" loading="lazy"></button>
                    @else
                        <div class="ticket-media-empty"><i class="bi bi-image" aria-hidden="true"></i><p>No payment receipt is available.</p></div>
                    @endif
                </div>
            </section>
        </div>
        <aside class="ticket-detail-aside" aria-label="Ticket management">
            <section class="admin-card">
                <div class="admin-card-header"><div><h2 class="admin-card-title">Ticket activity</h2><p class="admin-card-copy">Booking and admission history.</p></div></div>
                <ol class="ticket-activity">
                    <li><span class="ticket-activity-dot" aria-hidden="true"></span><strong>Request submitted</strong><time datetime="{{ $ticket->created_at->toIso8601String() }}">{{ $ticket->created_at->format('d M Y, g:i A') }}</time></li>
                    @if ($ticket->verified_at)
                        <li><span class="ticket-activity-dot" aria-hidden="true"></span><strong>Payment verified</strong><time datetime="{{ $ticket->verified_at->toIso8601String() }}">{{ $ticket->verified_at->format('d M Y, g:i A') }}</time></li>
                    @elseif ($ticket->status === 'pending')
                        <li class="ticket-activity-pending"><span class="ticket-activity-dot" aria-hidden="true"></span><strong>Awaiting verification</strong><p>Review the payment receipt to continue.</p></li>
                    @endif
                    @if ($ticket->status === 'rejected')
                        <li><span class="ticket-activity-dot" aria-hidden="true"></span><strong>Request rejected</strong><p>{{ $ticket->rejection_reason ?: 'No reason recorded.' }}</p></li>
                    @endif
                    @if ($ticket->checked_in_at)
                        <li><span class="ticket-activity-dot" aria-hidden="true"></span><strong>Checked in</strong><time datetime="{{ $ticket->checked_in_at->toIso8601String() }}">{{ $ticket->checked_in_at->format('d M Y, g:i A') }}</time></li>
                    @endif
                </ol>
                @if (in_array($ticket->status, ['verified', 'checked_in', 'rejected']))
                    <div class="ticket-management-actions">
                        <form method="POST" action="{{ route('admin.tickets.resendEmail', $ticket->id) }}">
                            @csrf
                            <button class="admin-btn admin-btn-outline" type="submit"><i class="bi bi-envelope-arrow-up" aria-hidden="true"></i> Resend email</button>
                        </form>
                        <p class="admin-card-copy">{{ $ticket->status === 'rejected' ? 'Sends the rejection reason only. No QR code.' : 'Sends the approval email with the ticket QR code.' }}</p>
                    </div>
                @endif
                @if ($ticket->status === 'pending')
                    <div class="ticket-management-actions">
                        <form method="POST" action="{{ route('admin.tickets.verify', $ticket->id) }}">@csrf<button type="submit" class="btn btn-success" onclick="return confirm('Verify this ticket and send the confirmation email?')"><i class="bi bi-check2-circle" aria-hidden="true"></i> Verify ticket</button></form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#detailRejectModal">Reject request</button>
                    </div>
                @elseif ($ticket->status === 'verified' && !$ticket->checked_in_at)
                    <div class="ticket-management-actions"><form method="POST" action="{{ route('admin.tickets.checkin', $ticket->id) }}">@csrf<button type="submit" class="admin-btn" onclick="return confirm('Check in this attendee?')"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Check in attendee</button></form></div>
                @endif
            </section>
            <section class="admin-card ticket-qr-panel">
                <div class="admin-card-header"><div><h2 class="admin-card-title">Ticket QR code</h2><p class="admin-card-copy">Reference for this booking.</p></div></div>
                @if ($ticket->qr_code_path && file_exists(public_path($ticket->qr_code_path)))
                    <div class="ticket-qr-image"><img src="{{ asset($ticket->qr_code_path) }}" alt="QR code for ticket {{ $ticket->ticket_id }}" width="180" height="180"></div>
                    <a class="admin-btn admin-btn-outline" href="{{ asset($ticket->qr_code_path) }}" download><i class="bi bi-download" aria-hidden="true"></i> Download QR code</a>
                @else
                    <div class="ticket-media-empty"><i class="bi bi-qr-code" aria-hidden="true"></i><p>No QR code is available.</p></div>
                @endif
                @if ($ticket->status !== 'verified' && $ticket->status !== 'checked_in')<p class="ticket-qr-note">A QR code does not confirm admission. The ticket must be verified first.</p>@endif
            </section>
        </aside>
    </div>
</div>
@if ($ticket->status === 'pending')
    <div class="modal fade" id="detailRejectModal" tabindex="-1" aria-labelledby="detailRejectTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-header"><h2 class="modal-title" id="detailRejectTitle">Reject ticket request</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form method="POST" action="{{ route('admin.tickets.reject', $ticket->id) }}">@csrf
                <div class="modal-body"><label for="detail-rejection-reason" class="form-label">Reason for rejection</label><textarea class="form-control" name="rejection_reason" id="detail-rejection-reason" rows="4" maxlength="1000" required>{{ old('rejection_reason') }}</textarea></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Reject ticket</button></div>
            </form>
        </div></div>
    </div>
@endif
@endsection

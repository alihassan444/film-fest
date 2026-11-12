@if ($tickets->count())
    @if ($tickets->contains('status', 'pending'))
        <form id="bulk-ticket-approval" method="POST" action="{{ route('admin.tickets.bulkVerify') }}" class="ticket-bulk-toolbar">
            @csrf
            <label><input type="checkbox" id="select-pending-page"> Select pending tickets on this page</label>
            <span id="bulk-selection-count" role="status">0 selected</span>
            <button class="admin-btn" type="submit" id="bulk-approve-button" disabled><i class="bi bi-check2-all" aria-hidden="true"></i> Approve selected &amp; email</button>
            <small>Up to 50 at once. Select 50 rows above to review a full batch.</small>
        </form>
    @endif
    <div class="ticket-table-scroll" role="region" aria-label="Ticket results; scroll horizontally on smaller screens" tabindex="0">
        <table class="table ticket-results-table">
            <caption class="visually-hidden">Ticket attendees, status, submission date, payment receipt, and available actions</caption>
            <thead><tr><th scope="col">Attendee</th><th scope="col">Ticket / Student</th><th scope="col">Status</th><th scope="col">Submitted</th><th scope="col">Payment</th><th scope="col">Actions</th></tr></thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td data-label="Attendee">@if ($ticket->status === 'pending')<input class="ticket-bulk-checkbox" type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" form="bulk-ticket-approval" aria-label="Select {{ $ticket->name }} for approval">@endif<a class="ticket-attendee-name" href="{{ route('admin.tickets.show', $ticket->ticket_id) }}">{{ $ticket->name }}</a><span class="ticket-cell-secondary">{{ $ticket->email }}</span>@if ($ticket->phone)<span class="ticket-cell-secondary">{{ $ticket->phone }}</span>@endif</td>
                        <td data-label="Ticket / Student"><a class="ticket-reference-link" href="{{ route('admin.tickets.show', $ticket->ticket_id) }}">{{ $ticket->ticket_id }}</a><span class="ticket-cell-secondary">{{ $ticket->student_id ?: 'No student ID' }}</span>@if ($ticket->department)<span class="ticket-cell-secondary">{{ $ticket->department }}@if ($ticket->semester) / Semester {{ $ticket->semester }}@endif</span>@endif</td>
                        <td data-label="Status"><span class="status-badge status-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>@if ($ticket->status === 'rejected' && $ticket->rejection_reason)<details class="ticket-reason"><summary>View reason</summary><p>{{ $ticket->rejection_reason }}</p></details>@endif</td>
                        <td data-label="Submitted"><time datetime="{{ $ticket->created_at->toIso8601String() }}">{{ $ticket->created_at->format('d M Y') }}<span class="ticket-cell-secondary">{{ $ticket->created_at->format('g:i A') }}</span></time></td>
                        <td data-label="Payment">@if ($ticket->transaction_screenshot)<button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#receiptPreviewModal" data-receipt-src="{{ asset($ticket->transaction_screenshot) }}" data-ticket-reference="{{ $ticket->ticket_id }}">Receipt <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i><span class="visually-hidden"> for {{ $ticket->name }} (opens preview)</span></button>@else<span class="ticket-cell-secondary">Not uploaded</span>@endif</td>
                        <td data-label="Actions"><div class="ticket-row-actions">
                            <a class="btn btn-secondary btn-sm" href="{{ route('admin.tickets.show', $ticket->ticket_id) }}">Details<span class="visually-hidden"> for {{ $ticket->name }}</span></a>
                            @if ($ticket->status === 'pending')
                                <form method="POST" action="{{ route('admin.tickets.verify', $ticket->id) }}">@csrf<button class="btn btn-success btn-sm" type="submit" onclick="return confirm('Verify this ticket and send the confirmation email?')">Verify<span class="visually-hidden"> {{ $ticket->ticket_id }}</span></button></form>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ticket->id }}">Reject<span class="visually-hidden"> {{ $ticket->ticket_id }}</span></button>
                            @elseif ($ticket->status === 'verified' && !$ticket->checked_in_at)
                                <form method="POST" action="{{ route('admin.tickets.checkin', $ticket->id) }}">@csrf<button class="btn btn-success btn-sm" type="submit" onclick="return confirm('Check in this attendee?')">Check in<span class="visually-hidden"> {{ $ticket->ticket_id }}</span></button></form>
                            @endif
                        </div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="ticket-directory-pagination"><p>Showing {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} of {{ number_format($tickets->total()) }} tickets</p>{{ $tickets->onEachSide(1)->links() }}</div>
    @foreach ($tickets as $ticket)
        @if ($ticket->status === 'pending')
            <div class="modal fade" id="rejectModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $ticket->id }}" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title" id="rejectModalLabel{{ $ticket->id }}">Reject ticket request</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <form method="POST" action="{{ route('admin.tickets.reject', $ticket->id) }}">@csrf<div class="modal-body"><p class="admin-card-copy mb-3">{{ $ticket->name }} / {{ $ticket->ticket_id }}</p><label class="form-label" for="rejection_reason{{ $ticket->id }}">Reason for rejection</label><textarea class="form-control" id="rejection_reason{{ $ticket->id }}" name="rejection_reason" rows="4" maxlength="1000" required></textarea></div><div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" type="submit">Reject ticket</button></div></form>
            </div></div></div>
        @endif
    @endforeach
@else
    <div class="admin-empty"><div class="admin-empty-icon"><i class="bi bi-search" aria-hidden="true"></i></div><h2 class="admin-card-title">No tickets found</h2><p>Try a different search or select another ticket status.</p></div>
@endif

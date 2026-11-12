@extends('layouts.admin')

@section('page-title', 'Pending tickets')

@section('content')
<div class="admin-ticket-page">
    <div class="row">
        <div class="col-12">
            <h1 class="admin-page-title">Pending Tickets</h1><p class="admin-page-subtitle mb-3">Review payment receipts and approve or reject ticket requests.</p>
            <a href="{{ route('admin.dashboard') }}" class="custom-btn-admin secondary mb-3"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Dashboard</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            
            <div class="card admin-section-card">
                <div class="card-body">
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Student ID</th>
                                        <th>CNIC</th>
                                        <th>Department</th>
                                        <th>Semester</th>
                                        <th>QR Code</th>
                                        <th>Transaction Screenshot</th>
                                        <th>Submitted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td><strong>{{ $ticket->ticket_id }}</strong></td>
                                            <td>{{ $ticket->name }}</td>
                                            <td>{{ $ticket->email }}</td>
                                            <td>{{ $ticket->phone ?? 'N/A' }}</td>
                                            <td>{{ $ticket->student_id ?? 'N/A' }}</td>
                                            <td>{{ $ticket->cnic ?? 'N/A' }}</td>
                                            <td>{{ $ticket->department ?? 'N/A' }}</td>
                                            <td>{{ $ticket->semester ?? 'N/A' }}</td>
                                            <td>
                                                @if($ticket->qr_code_path && file_exists(public_path($ticket->qr_code_path)))
                                                    <img src="{{ asset($ticket->qr_code_path) }}" 
                                                         alt="QR Code" 
                                                         class="qr-code-img" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#qrModal{{ $ticket->id }}">
                                                    
                                                    <!-- QR Code Modal -->
                                                    <div class="modal fade" id="qrModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $ticket->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="qrModalLabel{{ $ticket->id }}">QR Code - {{ $ticket->ticket_id }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ asset($ticket->qr_code_path) }}" 
                                                                         alt="QR Code" 
                                                                         class="qr-code-modal-img">
                                                                    <p class="mt-3"><strong>Ticket ID:</strong> {{ $ticket->ticket_id }}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <a href="{{ asset($ticket->qr_code_path) }}" download class="btn btn-primary">Download QR Code</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->transaction_screenshot)
                                                    <a href="{{ asset($ticket->transaction_screenshot) }}" target="_blank" class="btn btn-info btn-sm">View Screenshot</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form action="{{ route('admin.tickets.verify', $ticket->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to verify this ticket?')">Verify</button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ticket->id }}">Reject</button>
                                                </div>
                                                
                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $ticket->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="rejectModalLabel{{ $ticket->id }}">Reject Ticket</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('admin.tickets.reject', $ticket->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="rejection_reason{{ $ticket->id }}" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control" id="rejection_reason{{ $ticket->id }}" name="rejection_reason" rows="4" required placeholder="Please provide a reason for rejecting this ticket..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger">Reject Ticket</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $tickets->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">No pending tickets found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


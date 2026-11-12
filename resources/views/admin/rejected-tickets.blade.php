@extends('layouts.admin')

@section('page-title', 'Rejected tickets')

@section('content')
<div class="admin-ticket-page">
    <div class="row">
        <div class="col-12">
            <h1 class="admin-page-title">Rejected Tickets</h1><p class="admin-page-subtitle mb-3">Review rejected requests and the reasons recorded by your team.</p>
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
                                        <th>Status</th>
                                        <th>Rejection Reason</th>
                                        <th>QR Code</th>
                                        <th>Transaction Screenshot</th>
                                        <th>Submitted At</th>
                                        <th>Rejected At</th>
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
                                                <span class="status-badge">Rejected</span>
                                            </td>
                                            <td>
                                                <div class="rejection-reason">
                                                    {{ $ticket->rejection_reason ?? 'No reason provided' }}
                                                </div>
                                            </td>
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
                                            <td>{{ $ticket->updated_at->format('M d, Y H:i') }}</td>
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
                            <p class="text-muted">No rejected tickets found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


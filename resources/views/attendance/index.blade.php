<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance & Notes - {{ config('app.name', 'Ticket System') }}</title>
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        :root { --brand: #610b0c; --brand-soft: #d1ceba; }
        body { background: #f6f6f4; }
        .hero { background: linear-gradient(135deg, var(--brand), #873d3e); border-radius: 1rem; color: #fff; }
        .accordion-button:not(.collapsed) { background: #f3e8e8; color: var(--brand); }
        .brand-button { background: var(--brand); border-color: var(--brand); color: #fff; }
        .brand-button:hover { background: #430708; border-color: #430708; color: #fff; }
    </style>
</head>
<body>
<main class="container py-4 py-md-5">
    <section class="hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-2">Seminar Attendance & Notes</h1>
                <p class="mb-0 opacity-75">Browse attendance records by date or look up one student.</p>
            </div>
            <a class="btn btn-light" href="{{ route('index') }}">Back to ticket site</a>
        </div>
    </section>

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form class="row g-3 align-items-end" method="POST" action="{{ route('student.lookup') }}">
                @csrf
                <div class="col-md-9">
                    <label class="form-label" for="student_id">Student lookup</label>
                    <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                        <option value="">Choose a student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected(isset($selectedStudent) && $selectedStudent->id === $student->id)>
                                {{ $student->name }} — {{ $student->department }} / {{ $student->semester }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 d-grid"><button class="btn brand-button" type="submit">View history</button></div>
            </form>
        </div>
    </section>

    @isset($selectedStudent)
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <strong>{{ $selectedStudent->name }}</strong>
                <span class="text-muted">· {{ $selectedStudent->department }} · {{ $selectedStudent->semester }}</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Status</th><th>Seminar note</th></tr></thead>
                    <tbody>
                    @forelse($dateWiseData as $dateKey => $data)
                        @php($studentAttendance = $studentAttendances->get($dateKey))
                        <tr>
                            <td>{{ $data['date']->format('M d, Y') }}</td>
                            <td>
                                @if($studentAttendance)
                                    <span class="badge {{ $studentAttendance->status === 'Present' ? 'bg-success' : 'bg-danger' }}">{{ $studentAttendance->status }}</span>
                                @else
                                    <span class="badge bg-secondary">Not marked</span>
                                @endif
                            </td>
                            <td>{{ $data['note']?->content ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td class="text-center text-muted py-4" colspan="3">No attendance history is available.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endisset

    <section>
        <h2 class="h4 mb-3">All dates</h2>
        <div class="accordion" id="attendanceDates">
            @forelse($dateWiseData as $dateKey => $data)
                @php($attendanceMap = $data['attendances']->keyBy('student_id'))
                <div class="accordion-item mb-3 border-0 shadow-sm rounded overflow-hidden">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#date-{{ str_replace('-', '', $dateKey) }}">
                            <span class="fw-semibold">{{ $data['date']->format('l, F d, Y') }}</span>
                            <span class="badge bg-secondary ms-2">{{ $data['attendances']->count() }} marked</span>
                        </button>
                    </h3>
                    <div class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" id="date-{{ str_replace('-', '', $dateKey) }}" data-bs-parent="#attendanceDates">
                        <div class="accordion-body p-0">
                            <div class="p-4 border-bottom">
                                <h4 class="h6">Seminar note</h4>
                                <p class="mb-0">{{ $data['note']?->content ?? 'No note was added for this date.' }}</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead><tr><th>Name</th><th>Department</th><th>Semester</th><th>Role / Head</th><th>Status</th></tr></thead>
                                    <tbody>
                                    @foreach($students as $student)
                                        @php($attendance = $attendanceMap->get($student->id))
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->department }}</td>
                                            <td>{{ $student->semester }}</td>
                                            <td>{{ ucfirst($student->designation) }}@if($student->head) · {{ $student->head->name }}@endif</td>
                                            <td>
                                                <span class="badge {{ !$attendance ? 'bg-secondary' : ($attendance->status === 'Present' ? 'bg-success' : 'bg-danger') }}">
                                                    {{ $attendance?->status ?? 'Not marked' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">No attendance records or notes have been added yet.</div></div>
            @endforelse
        </div>
    </section>
</main>
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
</body>
</html>

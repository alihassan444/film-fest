@extends('layouts.admin')

@section('page-title', 'Attendance & notes')

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-title">Attendance & Notes Management</h1>
            <p class="admin-page-subtitle">Keep seminar attendance and notes in one considered space.</p>
        </div>
    </div>

    <section class="workspace-strip">
        <div class="workspace-strip-copy">
            <span class="admin-icon-tile" style="background:#fff"><i class="bi bi-calendar-check"></i></span>
            <div><h2>Seminar workspace</h2><p>Select a date to manage attendance and notes.</p></div>
        </div>
        <form class="workspace-date" method="GET" action="{{ route('admin.attendance') }}">
            <label for="seminar_date">Seminar date</label>
            <input class="admin-input" id="seminar_date" name="date" type="date" value="{{ $selectedDate }}" onchange="this.form.submit()">
        </form>
    </section>

    <div class="attendance-grid">
        <section class="admin-card attendance-panel">
            <div class="admin-card-header">
                <div><h2 class="admin-card-title">Student attendance</h2><p class="admin-card-copy">Mark attendance for the selected seminar.</p></div>
                <span class="admin-badge">{{ $students->count() }} {{ Str::plural('student', $students->count()) }}</span>
            </div>

            @if($students->isEmpty())
                <div class="admin-empty attendance-table-wrap">
                    <div class="admin-empty-icon"><i class="bi bi-people"></i></div>
                    <h3>No students registered yet</h3>
                    <p>Add students to your directory first. They’ll appear here, ready for attendance.</p>
                    <a class="admin-btn admin-btn-outline" href="{{ route('admin.students.index') }}"><i class="bi bi-arrow-up-right"></i>Manage students</a>
                </div>
                <div class="admin-card-footer"><span>No attendance to save</span><button class="admin-btn" disabled type="button"><i class="bi bi-check2"></i>Save attendance</button></div>
            @else
                <form method="POST" action="{{ route('admin.markAttendance') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                    <div class="admin-table-responsive attendance-table-wrap">
                        <table class="admin-table">
                            <thead><tr><th>Student</th><th>Department</th><th>Role</th><th>Attendance</th></tr></thead>
                            <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td><strong>{{ $student->name }}</strong>@if($student->head)<div class="text-muted small mt-1">Team: {{ $student->head->name }}</div>@endif</td>
                                    <td>{{ $student->department }}<div class="text-muted small mt-1">Semester {{ $student->semester }}</div></td>
                                    <td><span class="student-role">{{ ucfirst($student->designation) }}</span></td>
                                    <td>
                                        <select class="admin-select attendance-select" name="attendance[{{ $student->id }}]">
                                            <option value="Present" @selected($student->today_attendance === 'Present')>Present</option>
                                            <option value="Absent" @selected($student->today_attendance === 'Absent')>Absent</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="admin-card-footer">
                        <span>{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</span>
                        <button class="admin-btn" type="submit"><i class="bi bi-check2"></i>Save attendance</button>
                    </div>
                </form>
            @endif
        </section>

        <section class="admin-card attendance-panel">
            <div class="admin-card-header">
                <div><h2 class="admin-card-title">Seminar notes</h2><p class="admin-card-copy">A dedicated space for observations from this date.</p></div>
                <span class="admin-icon-tile"><i class="bi bi-journal-plus"></i></span>
            </div>
            <form method="POST" action="{{ $note ? route('admin.updateNote', $note) : route('admin.saveNote') }}">
                @csrf
                @if($note) @method('PUT') @endif
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <div class="admin-card-body">
                    <label class="admin-form-label" for="content">Notes for {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}</label>
                    <div class="note-toolbar" aria-hidden="true"><i class="bi bi-type-bold"></i><i class="bi bi-type-italic"></i><i class="bi bi-type-underline"></i><i class="bi bi-list-ul"></i><i class="bi bi-list-ol"></i></div>
                    <textarea class="admin-textarea note-editor" id="content" name="content" maxlength="5000" placeholder="Write a note..." required>{{ old('content', $note?->content) }}</textarea>
                    <div class="note-meta"><span><i class="bi bi-info-circle me-2"></i>One shared seminar note is saved per date.</span><span id="noteCount">0 characters</span></div>
                </div>
                <div class="admin-card-footer">
                    <span>{{ $note ? 'Last updated '.$note->updated_at->diffForHumans() : 'No note saved for this date' }}</span>
                    <button class="admin-btn" type="submit"><i class="bi bi-check2"></i>{{ $note ? 'Update note' : 'Save note' }}</button>
                </div>
            </form>
        </section>
    </div>

    @if($allNotes->isNotEmpty())
        <section class="admin-card notes-history">
            <div class="admin-card-header"><div><h2 class="admin-card-title">Previous notes</h2><p class="admin-card-copy">Review or remove notes from earlier seminar dates.</p></div><span class="admin-badge">{{ $allNotes->count() }}</span></div>
            <div class="admin-card-body">
                @foreach($allNotes as $oldNote)
                    <div class="note-history-row">
                        <div class="note-history-date">{{ $oldNote->date->format('d M Y') }}</div>
                        <div class="note-history-content">{{ Str::limit($oldNote->content, 180) }}</div>
                        <div class="note-history-actions">
                            <a class="admin-btn admin-btn-outline" href="{{ route('admin.attendance', ['date' => $oldNote->date->toDateString()]) }}">Open</a>
                            <form method="POST" action="{{ route('admin.deleteNote', $oldNote) }}" onsubmit="return confirm('Delete this seminar note?')">
                                @csrf @method('DELETE')
                                <button class="admin-btn admin-btn-danger" type="submit"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="attendance-hint"><i class="bi bi-lightbulb"></i><span>Start with your student directory, then return here to record attendance and notes.</span></div>
@endsection

@push('scripts')
<script>
    (() => {
        const note = document.getElementById('content');
        const count = document.getElementById('noteCount');
        const updateCount = () => count.textContent = `${note.value.length} characters`;
        note?.addEventListener('input', updateCount);
        if (note && count) updateCount();
    })();
</script>
@endpush

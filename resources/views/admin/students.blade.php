@extends('layouts.admin')

@section('page-title', 'Students')

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-title">Students</h1>
            <p class="admin-page-subtitle">Your people, thoughtfully organised. Add and manage your student community.</p>
        </div>
        <span class="admin-count-pill"><i class="bi bi-people"></i>{{ $students->count() }} {{ Str::plural('student', $students->count()) }}</span>
    </div>

    <div class="students-grid">
        <section class="admin-card student-form-card">
            <div class="admin-card-header">
                <div class="d-flex gap-3 align-items-center">
                    <span class="admin-icon-tile"><i class="bi bi-person-plus"></i></span>
                    <div><h2 class="admin-card-title">Add student</h2><p class="admin-card-copy">Create a new student profile.</p></div>
                </div>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.storeStudent') }}" method="POST">
                    @csrf
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="name">Name</label>
                        <input class="admin-input" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="admin-field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="department">Department</label>
                        <input class="admin-input" id="department" name="department" value="{{ old('department') }}" required>
                        @error('department')<div class="admin-field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="semester">Semester</label>
                        <input class="admin-input" id="semester" name="semester" value="{{ old('semester') }}" required>
                        @error('semester')<div class="admin-field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="designation">Designation</label>
                        <select class="admin-select" id="designation" name="designation" required>
                            <option value="member" @selected(old('designation', $heads->isEmpty() ? 'head' : 'member') === 'member')>Member</option>
                            <option value="head" @selected(old('designation', $heads->isEmpty() ? 'head' : 'member') === 'head')>Head</option>
                        </select>
                    </div>
                    <div class="admin-form-group" id="headField">
                        <label class="admin-form-label" for="head_id">Team head</label>
                        <select class="admin-select" id="head_id" name="head_id">
                            <option value="">Select a head</option>
                            @foreach($heads as $head)
                                <option value="{{ $head->id }}" @selected((string) old('head_id') === (string) $head->id)>{{ $head->name }}</option>
                            @endforeach
                        </select>
                        @if($heads->isEmpty())<div class="note-meta"><span>Add a head before assigning team members.</span></div>@endif
                    </div>
                    <button class="admin-btn w-100" type="submit"><i class="bi bi-plus-lg"></i>Add student</button>
                </form>
            </div>
        </section>

        <section class="admin-card student-list-card">
            <div class="admin-card-header">
                <div><h2 class="admin-card-title">Student list</h2><p class="admin-card-copy">All registered students in your workspace.</p></div>
                <span class="admin-badge">{{ $students->count() }}</span>
            </div>

            @if($students->isEmpty())
                <div class="admin-empty student-empty">
                    <div class="admin-empty-icon"><i class="bi bi-people"></i></div>
                    <h3>Your student community starts here</h3>
                    <p>No students have been added. Use the form to add your first student and bring your directory to life.</p>
                    <button class="admin-count-pill border-0" type="button" onclick="document.getElementById('name').focus()"><i class="bi bi-arrow-left"></i>Start with the Add student form</button>
                </div>
            @else
                <div class="admin-table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Name</th><th>Department</th><th>Semester</th><th>Role</th><th>Team head</th><th></th></tr></thead>
                        <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td><strong>{{ $student->name }}</strong></td>
                                <td>{{ $student->department }}</td>
                                <td>{{ $student->semester }}</td>
                                <td><span class="student-role">{{ ucfirst($student->designation) }}</span></td>
                                <td>{{ $student->head?->name ?? '—' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.deleteStudent', $student) }}" method="POST" onsubmit="return confirm('Delete this student and their attendance records?')">
                                        @csrf @method('DELETE')
                                        <button class="admin-btn admin-btn-danger" type="submit"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="admin-card-footer"><span>{{ $students->count() }} {{ Str::plural('student', $students->count()) }}</span><span>Student directory</span></div>
        </section>
    </div>

    <a class="directory-callout text-decoration-none" href="{{ route('admin.attendance') }}">
        <span class="directory-callout-copy"><i class="bi bi-journal-check"></i><span><strong>One directory. A connected workspace.</strong><span>Students you add here will be available in Attendance & Notes Management.</span></span></span>
        <i class="bi bi-arrow-up-right"></i>
    </a>
@endsection

@push('scripts')
<script>
    (() => {
        const designation = document.getElementById('designation');
        const headField = document.getElementById('headField');
        const headInput = document.getElementById('head_id');
        const hasHeads = {{ $heads->isNotEmpty() ? 'true' : 'false' }};
        const toggleHeadField = () => {
            const isMember = designation.value === 'member';
            headField.hidden = !isMember;
            headInput.required = isMember && hasHeads;
            if (!isMember) headInput.value = '';
        };
        designation?.addEventListener('change', toggleHeadField);
        toggleHeadField();
    })();
</script>
@endpush

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Note;
use App\Models\Student;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Show public, date-based attendance and seminar notes.
     */
    public function index()
    {
        return view('attendance.index', $this->attendanceData());
    }

    /**
     * Show the public attendance history for one student.
     */
    public function lookupStudent(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $student = Student::with('head')->findOrFail($validated['student_id']);
        $studentAttendances = Attendance::whereBelongsTo($student)
            ->orderByDesc('date')
            ->get()
            ->keyBy(fn (Attendance $attendance) => $attendance->date->format('Y-m-d'));

        return view('attendance.index', array_merge($this->attendanceData(), [
            'selectedStudent' => $student,
            'studentAttendances' => $studentAttendances,
        ]));
    }

    /**
     * Build the shared public attendance page data.
     *
     * @return array<string, mixed>
     */
    private function attendanceData(): array
    {
        $students = Student::with('head')->orderBy('name')->get();
        $notes = Note::orderByDesc('date')->get()
            ->keyBy(fn (Note $note) => $note->date->format('Y-m-d'));
        $attendances = Attendance::with('student.head')->orderByDesc('date')->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->date->format('Y-m-d'));

        $dates = $notes->keys()
            ->merge($attendances->keys())
            ->unique()
            ->sortDesc()
            ->values();

        $dateWiseData = $dates->mapWithKeys(function (string $date) use ($notes, $attendances) {
            return [$date => [
                'date' => \Carbon\Carbon::parse($date),
                'note' => $notes->get($date),
                'attendances' => $attendances->get($date, collect()),
            ]];
        });

        return compact('students', 'dateWiseData');
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\TicketVerified;
use App\Jobs\SendApprovedTicketEmail;
use App\Models\Attendance;
use App\Models\Note;
use App\Models\Student;
use App\Models\Ticket;
use App\Services\TicketDecisionMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get dashboard statistics.
     *
     * @return JsonResponse|\Illuminate\View\View
     */
    public function dashboard()
    {
        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::where('status', 'pending')->count();
        $awaitingEntryTickets = Ticket::where('status', 'verified')->count();
        $rejectedTickets = Ticket::where('status', 'rejected')->count();
        $checkedInTickets = Ticket::where('status', 'checked_in')->count();
        $verifiedTickets = $awaitingEntryTickets + $checkedInTickets;
        $approvedTickets = $verifiedTickets;
        $checkInRate = $approvedTickets ? round($checkedInTickets / $approvedTickets * 100) : 0;
        $studentCount = Student::count();

        return view('admin.dashboard', compact('totalTickets', 'pendingTickets', 'verifiedTickets', 'awaitingEntryTickets', 'rejectedTickets', 'checkedInTickets', 'approvedTickets', 'checkInRate', 'studentCount'));
    }

    /**
     * Show the attendance and notes management page.
     */
    public function attendance(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);
        $selectedDate = ! empty($validated['date'])
            ? \Carbon\Carbon::parse($validated['date'])->toDateString()
            : now()->toDateString();
        $students = Student::with(['head'])->orderBy('name')->get();

        // Get the selected date's note.
        $note = Note::where('date', $selectedDate)->first();

        // Get all notes ordered by date (newest first)
        $allNotes = Note::orderBy('date', 'desc')->get();

        // Get attendance for all students on the selected date.
        $attendances = Attendance::where('date', $selectedDate)
            ->pluck('status', 'student_id')
            ->toArray();

        // Attach the selected date's attendance to students.
        foreach ($students as $student) {
            $student->today_attendance = $attendances[$student->id] ?? null;
        }

        return view('admin.attendance', compact('students', 'note', 'allNotes', 'selectedDate'));
    }

    /**
     * Mark attendance for users.
     */
    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:Present,Absent',
            'date' => ['nullable', 'date'],
        ]);
        $attendance = $validated['attendance'];

        $studentIds = array_values(array_unique(array_map('intval', array_keys($attendance))));

        if (Student::whereKey($studentIds)->count() !== count($studentIds)) {
            throw ValidationException::withMessages([
                'attendance' => 'One or more selected students no longer exist.',
            ]);
        }

        $date = $validated['date'] ?? now()->toDateString();

        DB::transaction(function () use ($attendance, $date) {
            foreach ($attendance as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $date,
                    ],
                    [
                        'status' => $status,
                        'marked_at' => now(),
                    ]
                );
            }
        });

        return redirect()->route('admin.attendance', ['date' => $date])
            ->with('success', 'Attendance marked successfully for '.\Carbon\Carbon::parse($date)->format('M d, Y').'!');
    }

    /**
     * Show student and team management.
     */
    public function students()
    {
        $students = Student::with('head')->orderBy('name')->get();
        $heads = Student::where('designation', 'head')->orderBy('name')->get();

        return view('admin.students', compact('students', 'heads'));
    }

    /**
     * Save a seminar note for a date.
     */
    public function saveNote(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'date' => ['nullable', 'date'],
        ]);

        $date = $validated['date'] ?? now()->toDateString();

        Note::updateOrCreate(
            ['date' => $date],
            ['content' => $validated['content']]
        );

        return redirect()->route('admin.attendance', ['date' => $date])
            ->with('success', 'Seminar note saved for '.\Carbon\Carbon::parse($date)->format('M d, Y').'.');
    }

    /**
     * Update an existing seminar note.
     */
    public function updateNote(Request $request, Note $note)
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'date' => [
                'required',
                'date',
                Rule::unique('notes', 'date')->ignore($note->id),
            ],
        ]);

        $note->update($validated);

        return redirect()->route('admin.attendance', ['date' => $note->date->toDateString()])
            ->with('success', 'Seminar note updated for '.$note->date->format('M d, Y').'.');
    }

    /**
     * Delete a seminar note.
     */
    public function deleteNote(Note $note)
    {
        $date = $note->date->format('M d, Y');
        $note->delete();

        return redirect()->route('admin.attendance')
            ->with('success', "Seminar note for {$date} deleted.");
    }

    /**
     * Add a student to an attendance team.
     */
    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:255'],
            'designation' => ['required', Rule::in(['head', 'member'])],
            'head_id' => [
                'nullable',
                'required_if:designation,member',
                Rule::exists('students', 'id')->where('designation', 'head'),
            ],
        ]);

        if ($validated['designation'] === 'head') {
            $validated['head_id'] = null;
        }

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student added successfully.');
    }

    /**
     * Remove a student. Related attendance records are deleted by the database.
     */
    public function deleteStudent(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Display pending tickets.
     *
     * @return \Illuminate\View\View
     */
    public function pendingTickets(Request $request)
    {
        return $this->ticketListing($request, 'pending');
    }

    public function rejectedTickets(Request $request)
    {
        return $this->ticketListing($request, 'rejected');
    }

    public function allTickets(Request $request)
    {
        return $this->ticketListing($request);
    }

    private function ticketListing(Request $request, ?string $fixedStatus = null)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['pending', 'verified', 'rejected', 'checked_in'])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'name'])],
            'per_page' => ['nullable', Rule::in([15, 30, 50])],
        ]);
        $activeStatus = $fixedStatus ?? ($filters['status'] ?? null);
        $query = Ticket::query();
        if ($activeStatus) {
            if ($activeStatus === 'verified') {
                $query->whereIn('status', ['verified', 'checked_in']);
            } else {
                $query->where('status', $activeStatus);
            }
        }
        if ($term = trim($filters['q'] ?? '')) {
            $query->where(function ($search) use ($term) {
                foreach (['name', 'email', 'ticket_id', 'student_id', 'phone'] as $column) {
                    $search->orWhere($column, 'like', '%'.$term.'%');
                }
            });
        }
        if ($email = trim($filters['email'] ?? '')) {
            $query->where('email', 'like', '%'.$email.'%');
        }
        match ($filters['sort'] ?? 'newest') {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'name' => $query->orderBy('name')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
        $tickets = $query->paginate((int) ($filters['per_page'] ?? 15))->withQueryString();
        $statusCounts = Ticket::selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $listingTitle = match ($activeStatus) {
            'pending' => 'Pending tickets',
            'verified' => 'Verified tickets',
            'rejected' => 'Rejected tickets',
            'checked_in' => 'Checked-in tickets',
            default => 'All tickets',
        };
        if ($request->ajax()) {
            return response()->json(['html' => view('admin.partials.tickets-table', compact('tickets'))->render(), 'filters' => $filters]);
        }
        return view('admin.ticket-list', compact('tickets', 'activeStatus', 'fixedStatus', 'statusCounts', 'listingTitle'));
    }
    public function verifyTicket($id)
    {
        $ticket = Ticket::findOrFail($id);

        $updated = Ticket::whereKey($ticket->id)->where('status', 'pending')->update([
            'verification_token' => Str::uuid()->toString(),
            'status' => 'verified',
            'verified_at' => now(),
        ]);
        if (! $updated) {
            return redirect()->back()->with('error', 'Ticket is not in pending status.');
        }

        try {
            // The listener sends synchronously before this request completes.
            event(new TicketVerified($ticket->refresh()));
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->back()->with('error', 'Ticket approved, but the email could not be sent. Check the mail configuration, then use Resend email on ticket details.');
        }

        return redirect()->back()->with('success', 'Ticket verified successfully! Email sent to student.');
    }

    public function bulkVerifyTickets(Request $request, TicketDecisionMailer $mailer)
    {
        $validated = $request->validate([
            'ticket_ids' => ['required', 'array', 'min:1', 'max:50'],
            'ticket_ids.*' => ['required', 'integer', 'distinct', 'exists:tickets,id'],
        ]);
        try {
            $mailer->ensureDeliveryConfigured();
        } catch (\RuntimeException $exception) {
            return redirect()->back()->with('error', 'Configure SMTP delivery before bulk approval. No tickets were changed.');
        }
        if (in_array(config('queue.default'), ['sync', 'null'], true) && ! Queue::isFake()) {
            return redirect()->back()->with('error', 'Configure a persistent queue and start the ticket email worker before bulk approval. No tickets were changed.');
        }
        $approved = DB::transaction(function () use ($validated) {
            $tickets = Ticket::whereIn('id', $validated['ticket_ids'])->where('status', 'pending')->lockForUpdate()->get();
            foreach ($tickets as $ticket) {
                $ticket->update(['status' => 'verified', 'verified_at' => now(), 'verification_token' => Str::uuid()->toString()]);
                SendApprovedTicketEmail::dispatch($ticket->id, $ticket->verification_token)->afterCommit();
            }
            return $tickets->count();
        });

        if (! $approved) {
            return redirect()->back()->with('error', 'None of the selected tickets are pending. No additional emails were queued.');
        }

        return redirect()->back()->with('success', $approved.' tickets approved. Their QR emails are queued for immediate worker processing.');
    }

    /**
     * Reject a ticket.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectTicket(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $ticket = Ticket::findOrFail($id);

        $updated = Ticket::whereKey($ticket->id)->where('status', 'pending')->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        if (! $updated) {
            return redirect()->back()->with('error', 'Ticket is not in pending status.');
        }

        try {
            app(TicketDecisionMailer::class)->send($ticket->refresh());
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->back()->with('error', 'Ticket rejected, but the rejection email could not be sent. Check the mail configuration, then use Resend email on ticket details.');
        }

        return redirect()->back()->with('success', 'Ticket rejected. The reason has been emailed to the attendee without a QR code.');
    }

    public function resendTicketEmail($id)
    {
        $ticket = Ticket::findOrFail($id);
        if (! in_array($ticket->status, ['verified', 'checked_in', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Review this ticket before sending a decision email.');
        }
        try {
            app(TicketDecisionMailer::class)->send($ticket);
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->back()->with('error', 'Email could not be sent. Check the mail provider configuration and try again.');
        }

        return redirect()->back()->with('success', $ticket->status === 'rejected'
            ? 'Rejection email sent with the reason. No QR code was included.'
            : 'Approval email sent with the ticket QR code.');
    }

    /**
     * Check in a ticket.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkInTicket($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'verified') {
            return redirect()->back()->with('error', 'Only verified tickets can be checked in.');
        }

        if ($ticket->checked_in_at) {
            return redirect()->back()->with('error', 'This ticket has already been checked in.');
        }

        $ticket->status = 'checked_in';
        $ticket->checked_in_at = now();
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket checked in successfully at '.$ticket->checked_in_at->format('M d, Y H:i:s').'!');
    }

    public function show($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        return view('admin.ticket-detail', compact('ticket'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_attendance_page_is_available(): void
    {
        $this->get(route('attendance.index'))
            ->assertOk()
            ->assertSeeText('Seminar Attendance & Notes', false);
    }

    public function test_admin_can_manage_students_notes_and_attendance(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.storeStudent'), [
            'name' => 'Team Head',
            'department' => 'Film',
            'semester' => '8',
            'designation' => 'head',
        ])->assertRedirect(route('admin.students.index'));

        $head = Student::where('name', 'Team Head')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.storeStudent'), [
            'name' => 'Team Member',
            'department' => 'Film',
            'semester' => '4',
            'designation' => 'member',
            'head_id' => $head->id,
        ])->assertRedirect(route('admin.students.index'));

        $member = Student::where('name', 'Team Member')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.markAttendance'), [
            'attendance' => [
                $head->id => 'Present',
                $member->id => 'Absent',
            ],
        ])->assertRedirect(route('admin.attendance', ['date' => now()->toDateString()]));

        $this->actingAs($admin)->post(route('admin.saveNote'), [
            'date' => now()->toDateString(),
            'content' => 'Camera workshop',
        ])->assertRedirect(route('admin.attendance', ['date' => now()->toDateString()]));

        $this->actingAs($admin)->post(route('admin.markAttendance'), [
            'attendance' => [
                $head->id => 'Absent',
                $member->id => 'Present',
            ],
        ])->assertRedirect(route('admin.attendance', ['date' => now()->toDateString()]));

        $this->assertDatabaseHas('attendances', [
            'student_id' => $head->id,
            'status' => 'Absent',
            'date' => now()->toDateString(),
        ]);
        $this->assertDatabaseHas('notes', [
            'content' => 'Camera workshop',
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($admin)->get(route('admin.attendance'))
            ->assertOk()
            ->assertSee('Team Head');

        $this->post(route('student.lookup'), ['student_id' => $head->id])
            ->assertOk()
            ->assertSeeText('Camera workshop');
    }

    public function test_non_admin_cannot_open_attendance_management(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.attendance'))->assertForbidden();
    }
}

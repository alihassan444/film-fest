<?php

namespace Tests\Feature;

use Tests\TestCase;

class SubmissionPageTest extends TestCase
{
    public function test_submission_page_shows_flyer_details_and_registration_link(): void
    {
        $this->get('/submission')->assertOk()
            ->assertSee('https://forms.gle/6rJDdPbYW5a2U42aA')
            ->assertSee('9–11 December 2026')
            ->assertSee('15 November 2026')
            ->assertSee('30 November 2026')
            ->assertSee('1,000')->assertSee('1,500')
            ->assertSee('aria-current="page"', false);
    }

    public function test_home_page_links_to_submission_and_shows_updated_dates(): void
    {
        $this->get('/')->assertOk()
            ->assertSee(route('submission'))
            ->assertSee('9–11 December 2026')
            ->assertDontSee('Submission link coming soon');
    }
}

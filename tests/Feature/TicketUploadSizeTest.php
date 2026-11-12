<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketUploadSizeTest extends TestCase
{
    public function test_oversized_ticket_post_shows_a_helpful_page(): void
    {
        $this->call('POST', '/ticket', [], [], [], ['CONTENT_LENGTH' => (string) PHP_INT_MAX])
            ->assertStatus(413)
            ->assertSee('Upload too large')
            ->assertSee('5 MB')
            ->assertSee(route('form'));
    }

    public function test_oversized_json_post_returns_413(): void
    {
        $this->call('POST', '/ticket', [], [], [], [
            'CONTENT_LENGTH' => (string) PHP_INT_MAX,
            'HTTP_ACCEPT' => 'application/json',
        ])->assertStatus(413)->assertJsonStructure(['message']);
    }
}

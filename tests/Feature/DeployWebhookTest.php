<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeployWebhookTest extends TestCase
{
    public function test_webhook_handles_github_ping_event(): void
    {
        $response = $this->withHeaders([
            'X-GitHub-Event' => 'ping',
        ])->postJson('/webhook/deploy');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_webhook_ignores_non_main_branch(): void
    {
        $response = $this->withHeaders([
            'X-GitHub-Event' => 'push',
        ])->postJson('/webhook/deploy', [
            'ref' => 'refs/heads/develop',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeployWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        putenv('DEPLOY_WEBHOOK_SECRET=');
        config(['app.deploy_secret' => null]);
    }

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

    public function test_webhook_rejects_unauthorized_request_when_secret_is_configured(): void
    {
        config(['app.deploy_secret' => 'super-secure-secret-token']);

        $response = $this->postJson('/webhook/deploy');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }
}

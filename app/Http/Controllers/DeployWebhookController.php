<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployWebhookController extends Controller
{
    /**
     * Handle incoming GitHub Webhook or Direct Deployment Trigger.
     */
    public function handle(Request $request): JsonResponse
    {
        $configuredSecret = config('app.deploy_secret') ?: env('DEPLOY_WEBHOOK_SECRET');

        // If secret is set, validate it
        if (! empty($configuredSecret)) {
            $isAuthorized = false;

            // Method 1: Check GitHub X-Hub-Signature-256 HMAC
            $githubSignature = $request->header('X-Hub-Signature-256');
            if ($githubSignature) {
                $payload = $request->getContent();
                $expectedSignature = 'sha256='.hash_hmac('sha256', $payload, $configuredSecret);
                if (hash_equals($expectedSignature, $githubSignature)) {
                    $isAuthorized = true;
                }
            }

            // Method 2: Check query parameter `?secret=...` or custom header `X-Deploy-Secret`
            $querySecret = $request->query('secret') ?: $request->header('X-Deploy-Secret');
            if ($querySecret && hash_equals($configuredSecret, (string) $querySecret)) {
                $isAuthorized = true;
            }

            if (! $isAuthorized) {
                Log::warning('Unauthorized deployment webhook attempt', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid secret token or signature.',
                ], 403);
            }
        }

        // If this is a GitHub push event, check if it is for the main branch
        $event = $request->header('X-GitHub-Event');
        if ($event === 'ping') {
            return response()->json([
                'success' => true,
                'message' => 'GitHub Ping Webhook received successfully!',
            ]);
        }

        if ($event === 'push') {
            $ref = $request->input('ref');
            if ($ref && $ref !== 'refs/heads/main') {
                return response()->json([
                    'success' => true,
                    'message' => "Ignored push event for non-main branch: {$ref}",
                ]);
            }
        }

        $basePath = base_path();
        $deployScriptPath = $basePath.'/deploy.sh';

        $output = [];
        $returnCode = 0;

        if (file_exists($deployScriptPath) && is_readable($deployScriptPath)) {
            $command = "cd {$basePath} && bash deploy.sh 2>&1";
        } else {
            $command = "cd {$basePath} && git fetch origin main && git reset --hard origin/main && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache 2>&1";
        }

        exec($command, $output, $returnCode);

        $logOutput = implode("\n", $output);

        Log::info('Automated Webhook Deploy Triggered', [
            'return_code' => $returnCode,
            'output' => $logOutput,
        ]);

        return response()->json([
            'success' => $returnCode === 0,
            'message' => $returnCode === 0 ? 'Deployment completed successfully!' : 'Deployment finished with errors.',
            'return_code' => $returnCode,
            'output' => $output,
        ], $returnCode === 0 ? 200 : 500);
    }
}

<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendTelegramTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test {--message= : Nội dung tin nhắn tùy chọn}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra kết nối và gửi tin nhắn test đến Telegram của chủ quán';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService): int
    {
        $this->info('Đang kiểm tra kết nối Telegram Bot...');

        $customMessage = $this->option('message');
        $result = $telegramService->sendTestMessage($customMessage);

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            return Command::SUCCESS;
        }

        $this->error('❌ ' . $result['message']);
        $this->line('');
        $this->line('👉 Hướng dẫn cấu hình Telegram trong file .env:');
        $this->line('   TELEGRAM_BOT_TOKEN=your_bot_token_here');
        $this->line('   TELEGRAM_CHAT_ID=your_chat_id_here');
        $this->line('   TELEGRAM_NOTIFICATIONS_ENABLED=true');

        return Command::FAILURE;
    }
}

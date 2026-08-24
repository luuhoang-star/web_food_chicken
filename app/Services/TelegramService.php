<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    /**
     * Gửi thông báo đơn hàng mới về Telegram của chủ quán.
     */
    public function sendOrderNotification(Order $order): bool
    {
        $credentials = $this->getCredentials();

        if (! $credentials['enabled'] || empty($credentials['bot_token']) || empty($credentials['chat_id'])) {
            Log::info('Telegram notification skipped: Bot token or Chat ID is not configured.', [
                'order_code' => $order->order_code,
            ]);

            return false;
        }

        $message = $this->formatOrderMessage($order);

        return $this->sendMessage($credentials['bot_token'], $credentials['chat_id'], $message);
    }

    /**
     * Gửi tin nhắn test kiểm tra kết nối bot Telegram.
     */
    public function sendTestMessage(?string $customText = null): array
    {
        $credentials = $this->getCredentials();

        if (empty($credentials['bot_token'])) {
            return [
                'success' => false,
                'message' => 'Chưa cấu hình TELEGRAM_BOT_TOKEN trong file .env hoặc cài đặt website.',
            ];
        }

        if (empty($credentials['chat_id'])) {
            return [
                'success' => false,
                'message' => 'Chưa cấu hình TELEGRAM_CHAT_ID trong file .env hoặc cài đặt website.',
            ];
        }

        $text = $customText ?: "🍗 <b>GAO GÀ SỐT & CƠM - KẾT NỐI BOT THÀNH CÔNG!</b>\n\n"
            ."✅ Bot Telegram đã sẵn sàng nhận thông báo đơn hàng tự động từ website.\n"
            .'⏱️ Thời gian kiểm tra: <b>'.now()->format('H:i:s - d/m/Y')."</b>\n\n"
            .'🚀 <i>Chúc quán nhận bão đơn mỗi ngày!</i>';

        $success = $this->sendMessage($credentials['bot_token'], $credentials['chat_id'], $text);

        return [
            'success' => $success,
            'message' => $success
                ? 'Đã gửi tin nhắn test thành công! Hãy kiểm tra ứng dụng Telegram trên điện thoại của bạn.'
                : 'Gửi tin nhắn thất bại. Vui lòng kiểm tra lại Bot Token hoặc Chat ID.',
        ];
    }

    /**
     * Thực hiện HTTP POST gửi tin nhắn đến Telegram API.
     */
    protected function sendMessage(string $botToken, string $chatId, string $htmlContent): bool
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            $response = Http::timeout(5)->post($url, [
                'chat_id' => $chatId,
                'text' => $htmlContent,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('Telegram order notification sent successfully.', [
                    'chat_id' => $chatId,
                ]);

                return true;
            }

            Log::error('Telegram API Error: '.$response->body());

            return false;
        } catch (Throwable $e) {
            Log::error('Telegram Service Exception: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Định dạng tin nhắn HTML chi tiết, chuyên nghiệp cho đơn hàng.
     */
    protected function formatOrderMessage(Order $order): string
    {
        $order->loadMissing('items');

        $time = $order->created_at ? $order->created_at->format('H:i - d/m/Y') : now()->format('H:i - d/m/Y');
        $name = htmlspecialchars((string) $order->customer_name, ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars((string) $order->customer_phone, ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars((string) $order->address, ENT_QUOTES, 'UTF-8');
        $district = htmlspecialchars((string) $order->district, ENT_QUOTES, 'UTF-8');
        $payment = htmlspecialchars($order->payment_method_label, ENT_QUOTES, 'UTF-8');
        $note = $order->driver_note ? htmlspecialchars((string) $order->driver_note, ENT_QUOTES, 'UTF-8') : null;

        $msg = "🔔 <b>CÓ ĐƠN HÀNG MỚI TỪ WEBSITE!</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "🆔 <b>Mã đơn:</b> <code>#{$order->order_code}</code>\n";
        $msg .= "⏱️ <b>Thời gian:</b> {$time}\n\n";

        $msg .= "👤 <b>Khách hàng:</b> {$name}\n";
        $msg .= "📞 <b>SĐT:</b> <a href=\"tel:{$phone}\">{$phone}</a>\n";
        $msg .= "📍 <b>Địa chỉ:</b> {$address}, {$district}\n";

        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode($order->address.', '.$order->district.', Hà Nội');
        $msg .= "🗺️ <b>Chỉ đường:</b> <a href=\"{$mapsUrl}\">Mở Google Maps dẫn đường</a>\n";

        if ($note) {
            $msg .= "📝 <b>Ghi chú shipper:</b> <i>\"{$note}\"</i>\n";
        }

        $msg .= "💳 <b>Thanh toán:</b> {$payment}\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "🍗 <b>DANH SÁCH MÓN ĐẶT:</b>\n";

        foreach ($order->items as $index => $item) {
            $num = $index + 1;
            $itemName = htmlspecialchars((string) $item->product_name, ENT_QUOTES, 'UTF-8');
            $itemTotal = number_format((float) ($item->total_item_price ?: ($item->price * $item->quantity)), 0, ',', '.');

            $msg .= "  <b>{$num}. {$itemName}</b> x{$item->quantity} — <b>{$itemTotal}đ</b>\n";

            $details = [];
            if ($item->sauce) {
                $details[] = 'Sốt: '.htmlspecialchars($item->sauce, ENT_QUOTES, 'UTF-8');
            }
            if (! empty($item->toppings) && is_array($item->toppings)) {
                $details[] = 'Topping: '.htmlspecialchars(implode(', ', $item->toppings), ENT_QUOTES, 'UTF-8');
            }

            if (! empty($details)) {
                $msg .= '     └ <i>('.implode(' | ', $details).")</i>\n";
            }
        }

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $subtotal = number_format((float) $order->subtotal, 0, ',', '.');
        $shipping = (float) $order->shipping_fee === 0.0 ? '0đ (Freeship 3km)' : number_format((float) $order->shipping_fee, 0, ',', '.').'đ';
        $total = number_format((float) $order->total_amount, 0, ',', '.');

        $msg .= "💵 <b>Tạm tính:</b> {$subtotal}đ\n";
        $msg .= "🛵 <b>Phí ship:</b> {$shipping}\n";

        if ((float) $order->discount > 0) {
            $discount = number_format((float) $order->discount, 0, ',', '.');
            $msg .= "🏷️ <b>Giảm giá:</b> -{$discount}đ\n";
        }

        $msg .= "💰 <b>TỔNG TIỀN THU:</b> <b>{$total}đ</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= '👉 <a href="'.url('/tra-cuu-don?code='.urlencode($order->order_code)).'">Bấm vào đây để xem chi tiết đơn hàng</a>';

        return $msg;
    }

    /**
     * Lấy cấu hình Telegram từ SiteSettings hoặc Config .env
     */
    public function getCredentials(): array
    {
        $botToken = SiteSetting::get('telegram_bot_token', config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', '')));
        $chatId = SiteSetting::get('telegram_chat_id', config('services.telegram.chat_id', env('TELEGRAM_CHAT_ID', '')));
        $enabled = (bool) SiteSetting::get('telegram_notifications_enabled', config('services.telegram.enabled', env('TELEGRAM_NOTIFICATIONS_ENABLED', true)));

        return [
            'bot_token' => (string) $botToken,
            'chat_id' => (string) $chatId,
            'enabled' => $enabled,
        ];
    }
}

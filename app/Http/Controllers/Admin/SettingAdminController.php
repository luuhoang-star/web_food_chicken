<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingAdminController extends Controller
{
    /**
     * Giao diện cấu hình cài đặt quán toàn diện.
     */
    public function index(): View
    {
        $settings = SiteSetting::allKeyed();

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Cập nhật thông tin cài đặt.
     */
    public function update(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token', '_method', 'popup_banner_file', 'og_image_file', 'favicon_file']);

        // Xử lý upload ảnh Popup sự kiện
        if ($request->hasFile('popup_banner_file')) {
            $inputs['popup_banner_image'] = $this->uploadSettingImage($request->file('popup_banner_file'), 'popup');
        }

        // Xử lý upload ảnh chia sẻ OG Image
        if ($request->hasFile('og_image_file')) {
            $inputs['og_image'] = $this->uploadSettingImage($request->file('og_image_file'), 'og');
        }

        // Xử lý upload Favicon
        if ($request->hasFile('favicon_file')) {
            $inputs['favicon_url'] = $this->uploadSettingImage($request->file('favicon_file'), 'favicon');
        }

        foreach ($inputs as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) ($value ?? '')]
            );
        }

        $redirectTo = $request->input('_redirect_to', url()->previous());

        return redirect($redirectTo)->with('success', 'Đã lưu cấu hình cài đặt thành công!');
    }

    /**
     * Tải file ảnh cài đặt vào thư mục images/settings.
     */
    private function uploadSettingImage(UploadedFile $file, string $prefix): string
    {
        $uploadDir = public_path('images/settings');
        if (! File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }
        $fileName = "{$prefix}_".time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
        $file->move($uploadDir, $fileName);

        return 'images/settings/'.$fileName;
    }

    /**
     * Bật / Tắt nhanh trạng thái Nhận đơn của Bếp (Đang mở / Tạm dừng nhận đơn).
     */
    public function toggleStoreStatus(Request $request): JsonResponse|RedirectResponse
    {
        $currentStatus = SiteSetting::get('store_open_status', 'open');
        $newStatus = ($currentStatus === 'open') ? 'paused' : 'open';

        SiteSetting::updateOrCreate(
            ['key' => 'store_open_status'],
            ['value' => $newStatus]
        );

        $msg = ($newStatus === 'open')
            ? '🟢 Bếp đã MỞ LẠI - Website đang nhận đơn bình thường!'
            : '🔴 Bếp đã TẠM DỪNG NHẬN ĐƠN - Website tạm thời chặn khách đặt món để bạn xử lý đơn hiện tại!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'store_open_status' => $newStatus,
                'is_open' => $newStatus === 'open',
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Bắn tin nhắn test Telegram từ giao diện Admin.
     */
    public function testTelegram(TelegramService $telegramService): RedirectResponse
    {
        $result = $telegramService->sendTestMessage();

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}

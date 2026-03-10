<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $botUsername;
    protected string $apiBase;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''));
        $this->botUsername = config('services.telegram.bot_username', env('TELEGRAM_BOT_USERNAME', ''));
        $this->apiBase = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send a text message to a Telegram chat.
     */
    public function sendMessage(string $chatId, string $text): bool
    {
        if (empty($this->botToken) || empty($chatId)) {
            Log::warning('TelegramService: Missing bot token or chat ID', [
                'has_token' => !empty($this->botToken),
                'chat_id' => $chatId,
            ]);
            return false;
        }

        try {
            $response = Http::post("{$this->apiBase}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful() && $response->json('ok')) {
                Log::info('TelegramService: Message sent', ['chat_id' => $chatId]);
                return true;
            }

            Log::warning('TelegramService: API error', [
                'chat_id' => $chatId,
                'response' => $response->json(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('TelegramService: Exception', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate a Telegram deep link for user to connect their account.
     * When user clicks this link and sends /start, the bot receives the user ID.
     */
    public function generateStartLink(int $userId): string
    {
        return "https://t.me/{$this->botUsername}?start={$userId}";
    }

    /**
     * Check if the Telegram service is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->botUsername);
    }

    /**
     * Send proforma floated notification to a user.
     */
    public function sendProformaNotification(string $chatId, $proforma): bool
    {
        $brandName = $proforma->brand?->name ?? 'N/A';
        $text = "🔔 <b>New Proforma Available!</b>\n\n"
            . "📋 File: <b>{$proforma->file_number}</b>\n"
            . "🚗 Brand: {$brandName}\n"
            . "📌 Model: {$proforma->model} ({$proforma->year})\n"
            . "🪪 Plate: {$proforma->license_plate_number}\n"
            . "🔧 Type: " . ($proforma->isEteraCheretaMode() ? 'Etera Chereta' : 'Regular') . "\n\n"
            . "Login to your account to view and apply.";

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send proforma closed notification with billing info.
     */
    public function sendClosedNotification(string $chatId, $proforma): bool
    {
        $appCount = $proforma->applications()->count();
        $required = $proforma->required_number_of_shops ?: '∞';
        $text = "🔒 <b>Proforma Closed</b>\n\n"
            . "📋 File: <b>{$proforma->file_number}</b>\n"
            . "📊 Applications: {$appCount}/{$required}\n"
            . "🚗 {$proforma->brand?->name} {$proforma->model} ({$proforma->year})\n"
            . "🪪 Plate: {$proforma->license_plate_number}";

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send rejection notification.
     */
    public function sendRejectedNotification(string $chatId, $proforma): bool
    {
        $text = "❌ <b>Application Rejected</b>\n\n"
            . "📋 File: <b>{$proforma->file_number}</b>\n"
            . "🚗 {$proforma->brand?->name} {$proforma->model} ({$proforma->year})\n"
            . "Your application for this proforma has been rejected.";

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send "sent to owner" notification with invoice link.
     */
    public function sendSentToOwnerNotification(string $chatId, $proforma, string $invoiceUrl = ''): bool
    {
        $text = "✅ <b>Proforma Completed!</b>\n\n"
            . "📋 File: <b>{$proforma->file_number}</b>\n"
            . "🚗 {$proforma->brand?->name} {$proforma->model} ({$proforma->year})\n"
            . "Your proforma has been completed and returned to you.\n";

        if ($invoiceUrl) {
            $text .= "\n🧾 View Invoice: {$invoiceUrl}";
        }

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send application progress notification.
     */
    public function sendApplicationProgressNotification(string $chatId, $proforma): bool
    {
        $appCount = $proforma->applications()->count();
        $required = $proforma->required_number_of_shops ?: '∞';
        $text = "📋 <b>Application Update</b>\n\n"
            . "File: <b>{$proforma->file_number}</b>\n"
            . "Applications: {$appCount}/{$required} filled\n";

        if (is_numeric($required) && $appCount >= (int)$required) {
            $text .= "\n✅ All slots filled! You can now request close.";
        }

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send notification to the admin who floated a proforma when it is closed.
     */
    public function sendFloaterClosedNotification(string $chatId, $proforma): bool
    {
        $text = "📋 <b>Proforma Closed</b>\n\n"
            . "The proforma <b>{$proforma->file_number}</b> which you floated is closed.\n"
            . "Please Accept payment and send it back to owner.\n\n"
            . "🚗 {$proforma->brand?->name} {$proforma->model} ({$proforma->year})\n"
            . "🪪 Plate: {$proforma->license_plate_number}";

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send billing details to the poster via Telegram when proforma is closed.
     */
    public function sendBillingDetailsNotification(string $chatId, $proforma, float $charge, float $vatAmount, float $total): bool
    {
        $text = "🧾 <b>Proforma Billing Information</b>\n\n"
            . "📋 File: <b>{$proforma->file_number}</b>\n"
            . "🚗 {$proforma->brand?->name} {$proforma->model} ({$proforma->year})\n"
            . "🪪 Plate: {$proforma->license_plate_number}\n\n"
            . "💰 <b>Billing Summary:</b>\n"
            . "━━━━━━━━━━━━━━━━━━\n"
            . "Service Charge: " . number_format($charge, 2) . " Birr\n"
            . "VAT (15%): " . number_format($vatAmount, 2) . " Birr\n"
            . "━━━━━━━━━━━━━━━━━━\n"
            . "<b>Total: " . number_format($total, 2) . " Birr</b>\n\n"
            . "Your proforma has been closed. Thank you for using etera!";

        return $this->sendMessage($chatId, $text);
    }

    /**
     * Send notification to processed_by user that proforma is closed and needs payment collection.
     */
    public function sendProcessedByClosedNotification(string $chatId, $proforma): bool
    {
        $text = "📋 <b>Proforma Closed</b>\n\n"
            . "The proforma <b>{$proforma->file_number}</b> you processed is closed.\n"
            . "Please accept payment and send back to owner.\n\n"
            . "🚗 {$proforma->brand?->name} {$proforma->model} ({$proforma->year})\n"
            . "🪪 Plate: {$proforma->license_plate_number}";

        return $this->sendMessage($chatId, $text);
    }
}

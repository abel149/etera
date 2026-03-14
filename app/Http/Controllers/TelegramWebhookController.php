<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming Telegram webhook (bot updates).
     * When a user sends /start {userId}, we save their chat_id.
     */
    public function handle(Request $request)
    {
        $update = $request->all();

        Log::info('Telegram webhook received', ['update' => $update]);

        $callback = $update['callback_query'] ?? null;
        if ($callback) {
            $callbackId = $callback['id'] ?? null;
            $data = $callback['data'] ?? '';
            $chatId = $callback['message']['chat']['id'] ?? null;
            $messageId = $callback['message']['message_id'] ?? null;

            if ($callbackId) {
                try {
                    app(TelegramService::class)->answerCallbackQuery((string) $callbackId);
                } catch (\Throwable $e) {
                    Log::warning('Telegram callback: answerCallbackQuery failed', ['error' => $e->getMessage()]);
                }
            }

            if (is_string($data) && strpos($data, 'pw_reject:') === 0) {
                $identifier = substr($data, strlen('pw_reject:'));

                if (!empty($identifier)) {
                    $record = DB::table('password_reset_tokens')->where('email', $identifier)->first();
                    if ($record) {
                        DB::table('password_reset_tokens')->where('email', $identifier)->delete();
                    }
                }

                if ($chatId && $messageId) {
                    try {
                        app(TelegramService::class)->deleteMessage((string) $chatId, (int) $messageId);
                    } catch (\Throwable $e) {
                        Log::warning('Telegram callback: deleteMessage failed', ['error' => $e->getMessage()]);
                    }
                }

                return response()->json(['ok' => true]);
            }

            return response()->json(['ok' => true]);
        }

        // Extract message text and chat info
        $message = $update['message'] ?? null;
        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;
        $telegramName = $message['from']['first_name'] ?? 'User';

        if (!$chatId) {
            return response()->json(['ok' => true]);
        }

        // Handle /start command with user ID payload
        if (str_starts_with($text, '/start')) {
            $parts = explode(' ', $text, 2);
            $userId = isset($parts[1]) ? intval($parts[1]) : null;

            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    // Check if this telegram account is already linked to another user
                    $existing = User::where('telegram_chat_id', (string) $chatId)
                        ->where('id', '!=', $userId)
                        ->first();
                    if ($existing) {
                        // Send error via Telegram
                        $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''));
                        if ($botToken) {
                            \Illuminate\Support\Facades\Http::post(
                                "https://api.telegram.org/bot{$botToken}/sendMessage",
                                [
                                    'chat_id' => $chatId,
                                    'text' => "You've registered using this account please use other account",
                                ]
                            );
                        }
                        return response()->json(['ok' => true, 'duplicate' => true]);
                    }

                    $user->update(['telegram_chat_id' => (string) $chatId]);

                    Log::info('Telegram chat ID linked', [
                        'user_id' => $userId,
                        'chat_id' => $chatId,
                    ]);

                    // Send confirmation via Telegram Bot API
                    $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''));
                    if ($botToken) {
                        $confirmText = "✅ <b>Connected!</b>\n\n"
                            . "Hello {$telegramName}! Your Telegram is now linked to your etera account (<b>{$user->name}</b>).\n\n"
                            . "You will receive proforma notifications here.";

                        \Illuminate\Support\Facades\Http::post(
                            "https://api.telegram.org/bot{$botToken}/sendMessage",
                            [
                                'chat_id' => $chatId,
                                'text' => $confirmText,
                                'parse_mode' => 'HTML',
                            ]
                        );
                    }

                    return response()->json(['ok' => true, 'linked' => true]);
                }
            }

            // /start without valid user ID
            $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''));
            if ($botToken) {
                \Illuminate\Support\Facades\Http::post(
                    "https://api.telegram.org/bot{$botToken}/sendMessage",
                    [
                        'chat_id' => $chatId,
                        'text' => "👋 Welcome to etera Bot!\n\nPlease use the link from your etera account to connect.",
                        'parse_mode' => 'HTML',
                    ]
                );
            }
        }

        return response()->json(['ok' => true]);
    }
}

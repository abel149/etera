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

            if (is_string($data) && in_array($data, ['tg_disconnect', 'tg_disconnect_clearmsg', 'tg_back_to_dashboard'], true)) {
                try {
                    $user = null;
                    if ($chatId) {
                        $user = User::where('telegram_chat_id', (string) $chatId)->first();
                    }

                    if ($data === 'tg_back_to_dashboard') {
                    // Handle back to dashboard - send login URL
                    if ($chatId) {
                        $loginUrl = url('/login');
                        $text = "🔙 <b>Back to Your Account</b>\n\n"
                            . "Click the link below to log in to your etera account:\n"
                            . "{$loginUrl}\n\n"
                            . "You'll be redirected to your dashboard after logging in.";
                        app(TelegramService::class)->sendMessage((string) $chatId, $text);
                    }
                } else {
                    // Handle disconnect actions
                    if ($user) {
                        $user->update(['telegram_chat_id' => null]);
                    }

                    if ($data === 'tg_disconnect_clearmsg' && $chatId && $messageId) {
                        try {
                            app(TelegramService::class)->deleteMessage((string) $chatId, (int) $messageId);
                        } catch (\Throwable $e) {
                            Log::warning('Telegram disconnect: deleteMessage failed', ['error' => $e->getMessage()]);
                        }
                    }

                    if ($chatId) {
                        $text = "✅ Disconnected.\n\nTo connect again: log in to your etera account and use the Telegram connect button/page.\n\nIf you lost your old Telegram, this lets you connect a new one.";
                        app(TelegramService::class)->sendMessage((string) $chatId, $text);
                    }
                }
                } catch (\Throwable $e) {
                    Log::error('Telegram disconnect callback failed', ['error' => $e->getMessage()]);
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

        if (is_string($text) && trim($text) === '/end') {
            try {
                $user = User::where('telegram_chat_id', (string) $chatId)->first();
                if ($user) {
                    $user->update(['telegram_chat_id' => null]);
                    app(TelegramService::class)->sendMessage((string) $chatId, "✅ Disconnected.\n\nTo connect again: log in to your etera account and use the Telegram connect button/page.");
                } else {
                    app(TelegramService::class)->sendMessage((string) $chatId, "ℹ️ This Telegram chat is not linked to any etera account.");
                }
            } catch (\Throwable $e) {
                Log::warning('Telegram /end disconnect failed', ['error' => $e->getMessage()]);
            }

            return response()->json(['ok' => true]);
        }

        if (is_string($text) && (trim($text) === '/start' || trim($text) === '/settings')) {
            try {
                $user = User::where('telegram_chat_id', (string) $chatId)->first();
                if ($user) {
                    $manageText = "⚙️ <b>Telegram Connection</b>\n\n"
                        . "This Telegram is linked to etera account (<b>{$user->name}</b>).\n\n"
                        . "To disconnect anytime, type <b>/end</b> or use the button below.";

                    app(TelegramService::class)->sendMessageWithButtons((string) $chatId, $manageText, [
                        ['text' => 'Disconnect', 'callback_data' => 'tg_disconnect'],
                        ['text' => 'Disconnect & remove this message', 'callback_data' => 'tg_disconnect_clearmsg'],
                    ]);

                    return response()->json(['ok' => true]);
                }
            } catch (\Throwable $e) {
                Log::warning('Telegram manage connection message failed', ['error' => $e->getMessage()]);
            }
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
                            . "You will receive proforma notifications here.\n\n"
                            . "To disconnect anytime, type <b>/end</b> or use the button below.";

                        try {
                            app(TelegramService::class)->sendMessageWithButtons((string) $chatId, $confirmText, [
                                ['text' => '🔙 Back to Dashboard', 'callback_data' => 'tg_back_to_dashboard'],
                                ['text' => 'Disconnect', 'callback_data' => 'tg_disconnect'],
                                ['text' => 'Disconnect & remove this message', 'callback_data' => 'tg_disconnect_clearmsg'],
                            ]);
                        } catch (\Throwable $e) {
                            Log::warning('Telegram connect: sendMessageWithButtons failed, falling back to plain message', [
                                'user_id' => $userId,
                                'chat_id' => $chatId,
                                'error' => $e->getMessage(),
                            ]);

                            try {
                                \Illuminate\Support\Facades\Http::post(
                                    "https://api.telegram.org/bot{$botToken}/sendMessage",
                                    [
                                        'chat_id' => $chatId,
                                        'text' => $confirmText,
                                        'parse_mode' => 'HTML',
                                    ]
                                );
                            } catch (\Throwable $e2) {
                                Log::warning('Telegram connect: fallback sendMessage failed', ['error' => $e2->getMessage()]);
                            }
                        }
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

<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramPasswordResetLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $chatId;
    public string $resetUrl;
    public string $rejectUrl;

    public function __construct(string $chatId, string $resetUrl, string $rejectUrl)
    {
        $this->chatId = $chatId;
        $this->resetUrl = $resetUrl;
        $this->rejectUrl = $rejectUrl;
    }

    public function handle(TelegramService $telegramService): void
    {
        try {
            $telegramService->sendPasswordResetLink($this->chatId, $this->resetUrl, $this->rejectUrl);
        } catch (\Throwable $e) {
            Log::warning('SendTelegramPasswordResetLink failed', [
                'chat_id' => $this->chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

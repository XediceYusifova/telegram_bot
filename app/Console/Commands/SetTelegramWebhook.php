<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:webhook:set';
    protected $description = 'Set the Telegram webhook URL';

    public function handle()
    {
        $botToken = config('telegram.bot_token');
        $webhookUrl = 'https://example.com/your-webhook-endpoint'; // Bu URL, Laravel uygulamanızın uygun endpoint'ini temsil etmelidir.

        $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
            'url' => $webhookUrl,
        ]);

        if ($response->successful()) {
            $this->info('Webhook başarıyla ayarlandı.');
        } else {
            $this->error('Webhook ayarlanırken bir hata oluştu.');
            $this->error('Hata Mesajı: ' . $response->body());
        }
    }
}

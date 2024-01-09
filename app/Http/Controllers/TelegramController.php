<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Brand;

class TelegramController extends Controller
{
    // Bununla Telegram botumuzun son 100 güncəlləməsini göstərən bir JSON
    // https://api.telegram.org/bot6763100628:AAFRDw6t5mLcwC59TDJQBM9dr1P7eKP1dn0/getUpdates


    private array $chatIds;
    private string $botToken;

    public function __construct(array $chatIds, string $botToken)
    {
        $this->chatIds = $chatIds;
        $this->botToken = $botToken;
    }

    public function notificate($message, $approveCallback, $rejectCallback)
    {
        $responses = [];
        $successCount = 0;
        $failCount = 0;

        $keyboard = [
            'inline_keyboard' => [
                [
                    // ['text' => 'Google', 'url' => 'https://google.com'],
                    ['text' => 'Təsdiq et', 'callback_data' => $approveCallback],
                    ['text' => 'Geri çevir', 'callback_data' => $rejectCallback]
                ]
            ]
        ];

        foreach ($this->chatIds as $chatId) {
            $response = Http::withOptions(config('telegram.curl_options'))->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'reply_markup' => json_encode($keyboard)
            ]);

            $successCount = $response->successful() ? $successCount + 1 : $successCount;
            $failCount = !$response->successful() ? $failCount + 1 : $failCount;
            $responses[] = $response;
        }

        return [
            'responses' => $responses,
            'successCount' => $successCount,
            'failCount' => $failCount
        ];

        //server-də withOptions(['verify' => false])-> bunu sil
    }

    public function handleCallback(Request $request)
    {
        $data = $request->all();

        if (isset($data['callback_query'])) {
            // $callbackQuery = $data['callback_query'];
            // $callbackQueryId = $callbackQuery['id'];
            // $callbackData = $callbackQuery['data'];

            // // İlgili işlemleri gerçekleştir
            // if (strpos($callbackData, '/onayla') === 0) {
            //     $brandId = substr($callbackData, 8);
            //     $this->handleApprove($brandId);

            //     // Kullanıcının tıkladığı düğmeye bağlı olarak geri çağrı gönder
            //     $this->answerCallbackQuery($callbackQueryId, 'Onaylandı');
            // } elseif (strpos($callbackData, '/gericevir') === 0) {
            //     $brandId = substr($callbackData, 11);
            //     $this->handleReject($brandId);

            //     // Kullanıcının tıkladığı düğmeye bağlı olarak geri çağrı gönder
            //     $this->answerCallbackQuery($callbackQueryId, 'Geri çevrildi');
            // }
        }

        return response()->json(['status' => 'success']);
    }

    private function answerCallbackQuery($callbackQueryId, $text)
    {
        Http::withOptions(config('telegram.curl_options'))->post("https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]);
    }

    private function handleApprove($brandId)
    {
        // Təsdiq işlemlerini burada gerçekleştir
        // Örneğin:
        $brand = Brand::find($brandId);
        $brand->status = 0;
        $brand->save();
    }

    private function handleReject($brandId)
    {
        // Geri çevirme işlemlerini burada gerçekleştir
        // Örneğin:
        $brand = Brand::find($brandId);
        $brand->status = 0;
        $brand->save();
    }

    // public static function old_notificate($message)
    // {
    //     $parameters = [
    //         'chat_id' => '1273055068',
    //         'text' => $message,
    //     ];

    //     return self::send('sendMessage', $parameters);
    // }

    // public static function send($method, $parameters)
    // {
    //     $bot_token = config('telegram.bot_token');
    //     $url = "https://api.telegram.org/bot$bot_token/$method";

    //     if (!$curl = curl_init()) {
    //         exit();
    //     }

    //     $jsonParameters = json_encode($parameters);

    //     // SSL ayarları
    //     // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    //     // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

    //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    //     curl_setopt($curl, CURLOPT_POST, true);
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonParameters);
    //     curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    //     curl_setopt($curl, CURLOPT_URL, $url);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //     $result = curl_exec($curl);

    //     curl_close($curl);

    //     return $result;
    // }
}

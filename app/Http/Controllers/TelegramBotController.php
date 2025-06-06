<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;

class TelegramBotController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    public function webhook(Request $request)
    {
        $update = $this->telegram->getWebhookUpdate();
        $chatId = $update->getChat()->getId();
        $text = $update->getMessage()->getText();

        // Проверяем, является ли сообщение командой /start
        if ($text === '/start') {
            // Отправляем сообщение с chat_id
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Добро пожаловать. Ваш ID: " . $chatId,
            ]);
        } 
        // else {
        //     // Обработка других сообщений (по желанию)
        //     $this->telegram->sendMessage([
        //         'chat_id' => $chatId,
        //         'text' => "Вы написали: " . $text,
        //     ]);
        // }
    }
}

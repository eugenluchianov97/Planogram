<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class PollingCommand extends Command
{
    protected $signature = 'telegram:polling';
    protected $description = 'Polling Telegram updates';

    protected $telegram;

    public function __construct()
    {
        parent::__construct();
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    public function handle()
    {
        while (true) {
            $updates = $this->telegram->getUpdates();

            foreach ($updates as $update) {
                // Обработка обновлений
                $chatId = $update->getChat()->getId();
                $text = $update->getMessage()->getText();

                // Пример ответа
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Вы написали: " . $text,
                ]);
            }

            // Небольшая пауза перед следующим опросом
            sleep(1); // Задержка в 1 секунду
        }
    }
}

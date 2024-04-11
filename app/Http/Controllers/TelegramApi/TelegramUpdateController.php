<?php

namespace App\Http\Controllers\TelegramApi;


use App\Http\Controllers\TelegramApi\Types\TelegramCallbackQuery;
use App\Http\Controllers\TelegramApi\Types\TelegramMessages;

use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class TelegramUpdateController extends TelegramController
{
    public function run()
    {
        try {
            $this->client->on(function (Update $update) {
                $message = $update->getMessage() ?? $update->getCallbackQuery();

                /** Обработка Callback */
                if ($message instanceof CallbackQuery) {
                    $callbackController = new TelegramCallbackQuery();
                    $callbackController->callback_run(callbackQuery: $message);
                }

                /** Обработка входящих сообщений */
                if ($message instanceof Message) {
                    $messageController = new TelegramMessages();
                    $messageController->messages_run(message: $message);
                }

            }, function () {
                return true;
            });

            $this->client->run();

        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }
}

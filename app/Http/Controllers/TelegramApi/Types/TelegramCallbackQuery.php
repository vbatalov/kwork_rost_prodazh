<?php

namespace App\Http\Controllers\TelegramApi\Types;

use App\Http\Controllers\TelegramApi\TelegramController;
use App\Http\Traits\TelegramBotButtonTrait;
use App\Http\Traits\TelegramBotDataBuilderTrait;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\CallbackQuery;

class TelegramCallbackQuery extends TelegramController
{
    use TelegramBotDataBuilderTrait;
    use TelegramBotButtonTrait;

    public function callback_run(CallbackQuery $callbackQuery)
    {
        $cid = $callbackQuery->getMessage()->getChat()->getId();
        $message_id = $callbackQuery->getMessage()->getMessageId();

        $data = $this->decode($callbackQuery->getData());


        try {
            return $this->bot->answerCallbackQuery($callbackQuery->getId());
        } catch (Exception $e) {
            trigger_error($e);
        }

        return true;
    }

}

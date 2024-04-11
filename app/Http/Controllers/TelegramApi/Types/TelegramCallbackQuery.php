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

    /**
     * @throws Exception
     */
    public function callback_run(CallbackQuery $callbackQuery)
    {
        $cid = $callbackQuery->getMessage()->getChat()->getId();
        $message_id = $callbackQuery->getMessage()->getMessageId();

        $data = $this->decode($callbackQuery->getData());
        $method = $data['m'] ?? null;
        $subject = $data['s'] ?? null;


        if ($method == "getAsfInfo") {
            $message = new TelegramMessages();
            if (!$message->checkUserForGroupMember(chatId: env("GROUP_CHAT_ID"), cid: $cid)) {
                return $this->bot->answerCallbackQuery(callbackQueryId: $callbackQuery->getId(), text: "Вы не подписаны", showAlert: true);
            }

            if ($message->getResult(productIdFromString: $subject, cid: $cid, keyboard: $this->permanentKeyboard())) {
                return $this->bot->deleteMessage(chatId: $cid,messageId: $message_id);
            }
        }


        try {
            return $this->bot->answerCallbackQuery($callbackQuery->getId());
        } catch (Exception $e) {
            trigger_error($e);
        }

        return true;
    }

}

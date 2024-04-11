<?php

namespace App\Http\Controllers\TelegramApi\Types;


use App\Http\Controllers\TelegramApi\TelegramController;
use App\Http\Traits\TelegramBotButtonTrait;
use App\Http\Traits\TelegramBotDataBuilderTrait;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TelegramCallbackQuery extends TelegramController
{
    use TelegramBotDataBuilderTrait;
    use TelegramBotButtonTrait;

    public function callback_run(CallbackQuery $callbackQuery)
    {
        $data = $this->decode($callbackQuery->getData());

        $method = $data['m'] ?? null;
        $action = $data['a'] ?? null;
        $subject = $data['s'] ?? null;


        if ($method == "navigate") {
            $this->navigate($callbackQuery, $action);
        }

        try {
            return $this->bot->answerCallbackQuery($callbackQuery->getId());
        } catch (Exception $e) {
            trigger_error($e);
        }
    }

    /** Управление навигацией
     * @throws Exception
     */
    private function navigate(CallbackQuery $callbackQuery, string $action)
    {
        $cid = $callbackQuery->getMessage()->getChat()->getId();
        $message_id = $callbackQuery->getMessage()->getMessageId();


        /** Главное меню */
        if ($action == "menu") {
            $keyboard = new InlineKeyboardMarkup(
                [
                    [
                        $this->profile(),
                    ],
                    [
                        $this->setupNotification(),
                    ],
                    [
                        $this->supportButton(),
                        $this->about_bot()
                    ]
                ]);
            $message = view("TelegramBot.menu")->render();

            return $this->bot->editMessageText(chatId: $cid, messageId: $message_id, text: $message, parseMode: "HTML", replyMarkup: $keyboard);
        }

        if ($action == "notifications_lesegais") {

        }

        if ($action == "about_bot") {

        }

    }
}

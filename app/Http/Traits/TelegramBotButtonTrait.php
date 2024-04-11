<?php

namespace App\Http\Traits;


use JetBrains\PhpStorm\Pure;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

/**
 * При изменении текста констант, внести правки
 * app/Http/Controllers/TelegramApi/Types/TelegramMessages.php
 */
const START = "Узнать воронку в нише";
const HOW_IT_WORK = "Как это работает";
const ASK_QUESTION = "Задать вопрос";

trait TelegramBotButtonTrait
{
    use TelegramBotDataBuilderTrait;

    #[Pure]
    public function permanentKeyboard(): ReplyKeyboardMarkup
    {
        return new ReplyKeyboardMarkup(keyboard: [

            [
                START,
                HOW_IT_WORK
            ],
            [
                ASK_QUESTION
            ]

        ], oneTimeKeyboard: false, resizeKeyboard: true, selective: true, inputFieldPlaceholder: "Отправьте ссылку на товар или ID");
    }

    public function supportButton(): InlineKeyboardMarkup
    {
        return new InlineKeyboardMarkup([
            [
                ['text' => "Задать вопрос", 'url' => env("BOT_SUPPORT")]
            ]
        ]);
    }

    public function quotaIsOver(string $subject): InlineKeyboardMarkup
    {
        return new InlineKeyboardMarkup([
            [
                ['text' => "Перейти и подписаться", 'url' => env("GROUP_LINK")]
            ],
            [
                ['text' => "Я подписался", 'callback_data' => $this->build(method: "getAsfInfo", subject: $subject)]
            ]
        ]);
    }

}
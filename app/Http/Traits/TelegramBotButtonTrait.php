<?php

namespace App\Http\Traits;

use JetBrains\PhpStorm\ArrayShape;
use TelegramBot\Api\Types\WebAppData;

const MENU = "menu";
define("App\Http\Traits\WEB_URL", env("APP_URL"));

trait TelegramBotButtonTrait
{
    use TelegramBotDataBuilderTrait;

    #[ArrayShape(['text' => "string", 'callback_data' => "string"])]
    public function confirmAndStart(): array
    {
        return ['text' => 'Кнопка клавиатура', 'callback_data' => $this->build(method: "navigate", action: MENU)];
    }

    #[ArrayShape(['text' => "string", 'callback_data' => "string"])]
    public function menu(): array
    {
        return ['text' => 'Кнопка инлайн', 'callback_data' => $this->build(method: "navigate", action: MENU)];
    }

    /** Настройка уведомлений по сделкам */

    #[ArrayShape(['text' => "string", 'web_app' => "array"])]
    public function setupNotification(): array
    {
        $user_id = auth()->user()->id;
        $url = WEB_URL . "setupNotification/user/$user_id";
        return
            [
                'text' => 'Настроить уведомления', 'web_app' => [
                "url" => route("setup-notification.handle", ["user_id" => $user_id])
            ]
            ];
    }


    #[ArrayShape(['text' => "string", 'callback_data' => "string"])] public function profile(): array
    {
        return ['text' => 'Мой профиль', 'callback_data' => $this->build(method: "navigate", action: "profile")];
    }

    /** Поддержка */
    #[ArrayShape(['text' => "string", 'callback_data' => "string"])]
    public function about_bot(): array
    {
        return ['text' => 'Что умеет бот?', 'callback_data' => $this->build(method: "navigate", action: "about_bot")];
    }

    #[ArrayShape(['text' => "string", 'url' => "string"])]
    public function supportButton(): array
    {
        return ['text' => "Задать вопрос", 'url' => "https://t.me/vbatalov"];
    }

}
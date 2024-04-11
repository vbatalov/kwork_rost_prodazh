<?php

namespace App\Http\Controllers\TelegramApi\Types;

use App\Http\Controllers\TelegramApi\TelegramController;
use App\Models\User;

use Auth;
use TelegramBot\Api\Collection\CollectionItemInterface;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use TelegramBot\Api\Types\Message;

use App\Http\Traits\TelegramBotButtonTrait;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramCommands extends TelegramController
{
    use TelegramBotButtonTrait;

    public function run()
    {
        try {
            $this->client->command('start', function (Message $message) {
                $cid = $message->getChat()->getId();
                $last_name = $message->getChat()->getLastName();
                $first_name = $message->getChat()->getFirstName();
                $username = $message->getChat()->getUsername();

                if (!Auth::check()) {
                    $user = User::updateOrCreate(
                        [
                            "cid" => $cid,
                        ],
                        [
                            "last_name" => $last_name ?? null,
                            "first_name" => $first_name ?? null,
                            "username" => $username ?? null,
                            "cookie" => "start",
                            "quota" => env("USER_QUOTA") ?? 1,
                        ]
                    );

                    Auth::loginUsingId($user->id);
                }

                $keyboard = $this->permanentKeyboard();
                $photo = "https://randomwordgenerator.com/img/picture-generator/57e8d0424a55ad14f1dc8460962e33791c3ad6e04e50744172297cd5974ac0_640.jpg";


                $message = view("TelegramBot.start")->render();
                return $this->bot->sendPhoto(chatId: $cid, photo: $photo, caption: $message, replyMarkup: $keyboard, parseMode: "HTML");
            });

            $this->client->run();

        } catch (Exception $e) {
            trigger_error($e);
        }
    }
}

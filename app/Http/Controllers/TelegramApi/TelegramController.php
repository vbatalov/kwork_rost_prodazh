<?php

namespace App\Http\Controllers\TelegramApi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TelegramApi\Types\TelegramCommands;

use App\Http\Controllers\TelegramApi\Types\TelegramMessages;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\InvalidArgumentException;
use Throwable;


class TelegramController extends Controller
{
    public BotApi $bot;
    public Client $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->bot = new BotApi(env("BOT_TOKEN"));
        $this->client = new Client(env("BOT_TOKEN"));
    }

//    public function setCommand()
//    {
//        $array = ArrayOfBotCommand::fromResponse(
//            [
//                [
//                    "command" => "start",
//                    "description" => "Запустить",
//                ],
//                [
//                    "command" => "register",
//                    "description" => "Зарегистрироваться",
//                ],
//                [
//                    "command" => "restore_access",
//                    "description" => "Восстановить доступ",
//                ],
//                [
//                    "command" => "support",
//                    "description" => "Поддержка",
//                ],
//
//            ],
//        );
//        try {
//            $this->bot->setMyCommands($array);
//            return true;
//        } catch (HttpException | Exception $e) {
//            Log::error($e);
//            return false;
//        }
//    }

    public function register_bot()
    {
        $this->bot->setWebhook(url: env("BOT_URL"), dropPendingUpdates: true);
        return true;
    }

    public function getWebhookInfo()
    {
        return print_r($this->bot->getWebhookInfo());
    }


    public function handle(Request $request)
    {
        $cid = $this->auth_user($request->post());

        $command = new TelegramCommands();
        $command->run();

        if (Auth::check()) {
            try {
                $update = new TelegramUpdateController();
                $update->run();
            } catch (Throwable $t) {
                trigger_error($t);
            }
        } else {
            try {
                $this->bot->sendMessage("$cid", "Вы не авторизованы, нажмите /start");
            } catch (InvalidArgumentException | \TelegramBot\Api\Exception $e) {
                trigger_error($e);
            }
        }


    }

    private function auth_user($post)
    {
        if (!is_array($post)) $post = json_decode($post, true);

        $cid = null;

        if (isset($post['message']['from']['id'])) {
            $cid = $post['message']['from']['id'];
        } elseif (isset($post['callback_query']['from']['id'])) {
            $cid = $post['callback_query']['from']['id'];
        }

        if ($cid != null) {
            $user = User::where("cid", $cid)->first();
            if ($user != null) {
                Auth::loginUsingId($user->id);
            }

            return $cid;
        }

        return trigger_error("Error auth user");
    }
}

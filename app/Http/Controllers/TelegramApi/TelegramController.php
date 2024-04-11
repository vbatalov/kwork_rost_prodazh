<?php

namespace App\Http\Controllers\TelegramApi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TelegramApi\Types\TelegramCommands;


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


    /**
     * @throws \TelegramBot\Api\Exception
     */
    public function register_bot()
    {
        $this->bot->setWebhook(url: env("BOT_URL"), dropPendingUpdates: true);
        return true;
    }

    /**
     * @throws \TelegramBot\Api\Exception
     * @throws InvalidArgumentException
     */
    public function getWebhookInfo()
    {
        return $this->bot->getWebhookInfo();
    }


    public function handle(Request $request)
    {
        $this->auth_user($request->post());

        $command = new TelegramCommands();
        $command->run();

        if (Auth::check()) {
            try {
                $update = new TelegramUpdateController();
                $update->run();
            } catch (Throwable $t) {
                trigger_error($t);
            }
        }
    }

    private function auth_user($post)
    {
        if (!is_array($post)) $post = json_decode($post, true);

        if (isset($post['channel_post'])) return false;

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

            return true;
        }

        return trigger_error("Error auth user");
    }
}

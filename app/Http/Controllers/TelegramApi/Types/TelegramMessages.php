<?php

namespace App\Http\Controllers\TelegramApi\Types;


use App\Http\Controllers\TelegramApi\TelegramController;
use App\Http\Traits\TelegramBotButtonTrait;
use App\Http\Traits\WBApiTrait;

use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Message;

class TelegramMessages extends TelegramController
{
    use WBApiTrait;
    use TelegramBotButtonTrait;

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function run(Message $message)
    {
        $cid = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        $text = $message->getText();

        $keyboard = $this->permanentKeyboard();

        /** Обработка текстовых команд с клавиатуры */
        if ($text == "Узнать воронку в нише") {
            $message = view("TelegramBot.start")->render();
            return $this->bot->sendMessage(chatId: $cid, text: $message, parseMode: "HTML", replyMarkup: $keyboard);
        }
        if ($text == "Как это работает") {
            $message = view("TelegramBot._faq")->render();
            return $this->bot->sendMessage(chatId: $cid, text: $message, parseMode: "HTML", replyMarkup: $keyboard);
        }
        if ($text == "Задать вопрос") {
            $message = view("TelegramBot._askQuestion")->render();
            return $this->bot->sendMessage(chatId: $cid, text: $message, parseMode: "HTML", replyMarkup: $this->supportButton());
        }

        if ($message->getText() != "/start") {

            //Получить ID товара из сообщения пользователя
            if ($productIdFromString = $this->getProductIdFromString(string: $text)) {

                /**
                 * Проверка пользователя на участника сообщества
                 * Проверка пользователя на доступную квоту проверок (до подписки)
                 */
                if ((!$this->checkUserForGroupMember(chatId: env("GROUP_CHAT_ID"), cid: $cid)) and (auth()->user()->quota == 0)) {
                    $user_not_member = true;
                    return $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.result", compact("productIdFromString", "user_not_member"))->render(),
                        parseMode: "HTML", replyMarkup: $this->quotaIsOver($productIdFromString));
                }

                // Запрос на получения карточки по ID
                if ($result = $this->getCardById(id: $productIdFromString)) {
                    // Получение данных с API:Evirma
                    if ($result = $this->getAsfInfo(data: $result)) {
                        // Убрать одну бесплатную квоту на запрос
                        auth()->user()->takeAwayOneQuota();
                        return $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.result", compact("result", "productIdFromString"))->render(), parseMode: "HTML", replyMarkup: $keyboard);
                    } else {
                        $this->bot->sendMessage(chatId: "$cid", text: "Error Api", parseMode: "HTML", replyMarkup: $keyboard);
                    }
                } else {
                    // Если по ID ничего не найдено
                    $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.getCardById_Error")->render(), parseMode: "HTML");
                }
            } else {
                $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.getProductIdFromString_Error")->render(), parseMode: "HTML", replyMarkup: $keyboard);
            }
        }

        return true;
    }

    public function getCardById(string $id)
    {
        if ($response = $this->getCard_API(id: $id)) {
            $data = json_decode($response, true);

            if (!empty($data['data']['products'])) {

                $subject_id = $data['data']['products'][0]['subjectId'];

                $sizes = $data['data']['products'][0]['sizes']; // Массив с размерами
                $total_sizes = 0; // Всего размеров у продукта, для вычисления средней цены
                $total_price = 0; // Инициализация суммы цен

                foreach ($sizes as $size) {
                    if (isset($size['price']['product'])) {
                        $total_sizes += 1;
                        $total_price = $total_price + $size['price']['product'];
                    }
                }

                return [
                    "subject_id" => $subject_id,
                    "total_price" => $total_price,
                    "total_sizes" => $total_sizes,
                    "average_price_rub" => ($total_price / $total_sizes / 100),
                    "average_price_rub_plus_20_percent" => ($total_price / $total_sizes / 100) * 1.2,
                ];
            }
        }
        return false;
    }

    public function getAsfInfo(array $data)
    {
        if ($response = $this->getAsfInfo_API(data: $data)) {
            return json_decode($response->body(), true);
        }

        return false;
    }

    public function checkUserForGroupMember(string $chatId, string $cid)
    {

        try {
            $getChatMember = $this->bot->getChatMember(chatId: $chatId, userId: $cid);
            $status = $getChatMember->getStatus();

            if ($status == "left") {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }


    }

    private function getProductIdFromString($string): bool|string
    {
        /** Если отправлены только цифры, возвращаем цифры как ID */
        if ((is_numeric($string)) and (strlen($string) > 6)) {
            return $string;
        }

        // Разбираем URL на части
        $parts = explode('/', trim($string, '/'));

        // Проходимся по частям URL и ищем нужный нам идентификатор
        foreach ($parts as $index => $part) {
            if (str_contains($part, 'catalog') && is_numeric(trim($parts[$index + 1], '-'))) {
                // Возвращаем найденный идентификатор
                return trim($parts[$index + 1], '-');
            }
        }

        // Если идентификатор не найден, возвращаем false
        return false;
    }

}

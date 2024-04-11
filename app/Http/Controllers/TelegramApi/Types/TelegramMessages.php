<?php

namespace App\Http\Controllers\TelegramApi\Types;


use App\Http\Controllers\TelegramApi\TelegramController;
use App\Http\Traits\WBApiTrait;

use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\Types\Message;

class TelegramMessages extends TelegramController
{
    use WBApiTrait;

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function run(Message $message)
    {
        $cid = $message->getChat()->getId();
        $text = $message->getText();

        if ($message->getText() != "/start") {
            /** @var string $productIdFromString
             *  Получить ID товара из сообщения пользователя
             *  if false return getProductIdFromString_Error.blade
             */
            if ($productIdFromString = $this->getProductIdFromString(string: $text)) {

                /** Проверка пользователя на участника сообщества
                 * @param string chatId -> .env -> GROUP_CHAT_ID
                 * @param string cid -> chat id user telegram
                 * @return bool
                 */
                if (!$this->checkUserForGroupMember(chatId: env("GROUP_CHAT_ID"), cid: $cid)) {
                    $user_not_member = true;
                    return $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.result", compact("productIdFromString", "user_not_member"))->render(), parseMode: "HTML");
                }

                /** @var mixed $result
                 *  Запрос на получения карточки по ID, отправленному
                 *  if false return getCardById_Error.blade
                 */
                if ($result = $this->getCardById(id: $productIdFromString)) {
                    /** @var mixed $result
                     *  Получение данных с API:Evirma
                     */
                    if ($result = $this->getAsfInfo(data: $result)) {
                        $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.result", compact("result", "productIdFromString"))->render(), parseMode: "HTML");
                    } else {
                        $this->bot->sendMessage(chatId: "$cid", text: "Error Api", parseMode: "HTML");
                    }
                } else {
                    $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.getCardById_Error")->render(), parseMode: "HTML");
                }
            } else {
                $this->bot->sendMessage(chatId: "$cid", text: view("TelegramBot.getProductIdFromString_Error")->render(), parseMode: "HTML");
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
                /** Массив с размерами */
                $sizes = $data['data']['products'][0]['sizes'];
                /** Всего размеров у продукта, для вычисления средней цены */
                $total_sizes = 0;
                // Инициализация суммы цен
                $total_price = 0;
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

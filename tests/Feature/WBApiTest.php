<?php

namespace Tests\Feature;

use App\Http\Controllers\TelegramApi\TelegramController;
use App\Http\Controllers\TelegramApi\Types\TelegramMessages;
use App\Http\Traits\WBApiTrait;
use Tests\TestCase;

class WBApiTest extends TestCase
{
    use WBApiTrait;

    public function test_getCard_API_Trait(): void
    {
        $response = $this->getCard_API(id: "200592459");
        $this->assertTrue($response->successful());
        dump(json_decode($response->body(), true));
    }

    public function test_getCard_bot(): bool|array
    {
        $messages = new TelegramMessages();
        $result = $messages->getCardById("200592459");
        dump($result);

        $this->assertTrue(!empty($result));
        return $result;
    }

    public function test_getAsfInfo_API_bot()
    {
        $data = $this->test_getCard_bot();
//        dump($data);

        $messages = new TelegramMessages();
        $result = $messages->getAsfInfo(data: $data);
        dd($result);
    }

    public function test_getAsfInfo_self_data()
    {
        $messages = new TelegramMessages();
        $result = $messages->getAsfInfo(data: [
            "subject_id" => 3935,
            "average_price_rub_plus_20_percent" => 2844,
        ]);
        dd($result);
    }

    public function test_checkUserForGroupMember()
    {
        $my = "112865662";
        $other = "5434279594";
        $message = new TelegramMessages();
        $result = $message->checkUserForGroupMember(chatId: "-1001149309777", cid: "$my");

        dd($result);
    }
}

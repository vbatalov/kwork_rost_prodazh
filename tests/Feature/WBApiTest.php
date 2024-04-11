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
        $response = $this->getCard_API(id: "105199860");
        $this->assertTrue($response->successful());
        dump(json_decode($response->body(), true));
    }

    public function test_getCard_bot(): bool|array
    {
        $messages = new TelegramMessages();
        $result = $messages->getCardById("16522015");

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
}

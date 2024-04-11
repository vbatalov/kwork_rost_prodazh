<?php

namespace App\Http\Traits;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait WBApiTrait
{
    public function getCard_API(string $id): bool|PromiseInterface|Response
    {
        $url = "https://card.wb.ru/cards/v2/detail?appType=1&curr=rub&dest=12358289&spp=30&nm=$id";
        $response = Http::get(url: $url);

        if ($response->successful()) {
            return $response;
        }

        return false;
    }

    public function getAsfInfo_API(array $data): bool|PromiseInterface|Response
    {
        $url = 'https://evirma.ru/api/v1/data/asf-info';
        $response = Http::post(url: $url, data: [
            "subject" => $data['subject_id'],
            "price" => $data['average_price_rub_plus_20_percent']
        ]);

        if ($response->successful()) return $response;

        return false;
    }
}
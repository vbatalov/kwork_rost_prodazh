<?php

namespace App\Http\Traits;

trait TelegramBotDataBuilderTrait
{
    public function build($method, $action = null, $subject = null): string
    {
        return json_encode([
            "m" => $method,
            "a" => $action,
            "s" => $subject,
        ]);
    }

    public function decode(string $build): array
    {
        return json_decode($build, true);
    }
}
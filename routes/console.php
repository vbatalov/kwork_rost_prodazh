<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('bot:register', function () {
    $this->comment('Processing');
    $controller = new App\Http\Controllers\TelegramApi\TelegramController();
    if ($controller->register_bot()) {
       $this->comment('Webhook was set');
    } else {
        $this->comment('Error set webhook');
    }
});

Artisan::command('bot:info', function () {
    $this->comment('Processing');

    $controller = new App\Http\Controllers\TelegramApi\TelegramController();
    dump($controller->getWebhookInfo());
});

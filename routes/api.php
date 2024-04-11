<?php

use App\Http\Controllers\TelegramApi\TelegramController;
use Illuminate\Support\Facades\Route;

Route::post("telegram_bot", [TelegramController::class, "handle"]);

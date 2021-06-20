<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('(/start|/search|поиск)', BotManController::class.'@startConversation');

$botman->fallback(function($bot) {
    $bot->reply('Для пошуку скористайтесь командою /search');
});
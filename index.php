<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 25.10.2017
 * Time: 18:17
 */

header('Content-Type: text/html; charset=utf-8');

require_once('vendor/autoload.php');

    $token = "466539344:AAE9QgFeHOxqWvJfEPgWcEXGDSvHj2qCZeM";
    $bot = new \TelegramBot\Api\Client($token);

    if (!file_exists("registered.trigger")) {
        $page_url = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        $result = $bot->setWebhook("https://bottelegabot.herokuapp.com/");
        if($result) {
            file_put_contents("registered.trigger", time());
        }
    }

    $bot->command('start', function ($message) use ($bot) {
        $answer = 'Что я могу для вас сделать?';
        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
            [["text" => "Расписание"], ["text" => "Моё расписание"]],
            [["text" => "Оценить доклад"], ["text" => "Лидеры голосования"]],
            [["text" => "Подписаться на новости"]],
            [["text" => "Связаться с организаторами"]],
            [["text" => "О форуме"]],
        ], true, true);
        $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
    });

    $bot->command('help', function ($message) use ($bot) {
       $answer = 'Команды:
       /help - помощь';
       $bot->sendMessage($message->getChat()->getId(), $answer);
    });

    $bot->on(function($Update) use ($bot){
        $message = $Update->getMessage();
        $messageText = $message->getText();
        $chatId = $message->getChat()->getId();

        if(mb_stripos($messageText, "Расписание") !== false) {
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
               [["text" => "30 ноября"]],
               [["text" => "1 декабря"]],
            ], true, true);
            $answer = "Выберите дату:";
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }
    }, function ($message) use ($name){
        return true;
    });

    $bot->run();



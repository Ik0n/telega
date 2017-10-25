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
        $bot->sendMessage($message->getChat()->getId(), $answer);
    });

    $bot->command('help', function ($message) use ($bot) {
       $answer = 'Команды:
       /help - помощь';
       $bot->sendMessage($message->getChat()->getId(), $answer);
    });

    $bot->run();



<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 25.10.2017
 * Time: 18:17
 */

header('Content-Type: text/html; charset=utf-8');

require_once('vendor/autoload.php');

    function pg_connection_string() {
        return "dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require";
    }

    $db = pg_connect(pg_connection_string());
    if (!$db) {
        echo "Database connection error";
        exit;
    }



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

        var_dump($message);

        $answer = 'Что я могу для вас сделать?';
        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
            [["text" => "Расписание"], ["text" => "Моё расписание"]],
            [["text" => "Оценить доклад"], ["text" => "Лидеры голосования"]],
            [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
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

        if($messageText == "Расписание") {
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
               [["text" => "30 ноября"]],
               [["text" => "1 декабря"]],
               [["text" => "Меню"]],
            ], true, true);
            $answer = "Выберите дату:";
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if($messageText == "Меню") {
            $answer = 'Что я могу для вас сделать?';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "Расписание"], ["text" => "Моё расписание"]],
                [["text" => "Оценить доклад"], ["text" => "Лидеры голосования"]],
                [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
                [["text" => "Связаться с организаторами"]],
                [["text" => "О форуме"]],
            ], true, true);
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "О форуме") {
            $answer = 'Здесь содержится полезная информация о нашем форуме. 
                Выберите раздел:';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "О Гиперкубе"]],
                [["text" => "Биржа деловых контактов"]],
                [["text" => "Самое интересное"]],
                [["text" => "Как добраться"], ["text" => "Питание"]],
                [["text" => "Размещение"],["text" => "Меню"]],
            ], true, true);
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "О Гиперкубе") {
            $answer = '«Гиперкуб» − это центр городского развития «Сколково».
            Центр, в котором разрабатываются информационные, экономические, инженерные, 
            градостроительные и организационные модели будущего и царит атмосфера творчества, в которой реализуются любые идеи.';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "О Гиперкубе"]],
                [["text" => "Биржа деловых контактов"]],
                [["text" => "Самое интересное"]],
                [["text" => "Как добраться"], ["text" => "Питание"]],
                [["text" => "Размещение"],["text" => "Меню"]],
            ], true, true);
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "Как добраться") {
            $answer = 'Адрес: 
            ИЦ Сколково, ул. Малевича, д 1. Москва';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "О Гиперкубе"]],
                [["text" => "Биржа деловых контактов"]],
                [["text" => "Самое интересное"]],
                [["text" => "Как добраться"], ["text" => "Питание"]],
                [["text" => "Размещение"],["text" => "Меню"]],
            ], true, true);
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "Питание") {
            $answer = 'На территории мероприятия для Вас будут работать рестораны, кафе и фудтраки.';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "О Гиперкубе"]],
                [["text" => "Биржа деловых контактов"]],
                [["text" => "Самое интересное"]],
                [["text" => "Как добраться"], ["text" => "Питание"]],
                [["text" => "Размещение"],["text" => "Меню"]],
            ], true, true);
            $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "30 ноября") {

        }

        if ($messageText == "Спикеры") {
            $db = pg_connect(pg_connection_string());
            $results = pg_query($db, "SELECT id, name, about, refphoto FROM public.\"Speakers\";");
            $results = pg_fetch_all($results);

            foreach ($results as $result) {
                $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $result['name']);
                $bot->sendPhoto($message->getChat()->getId(), $result['refphoto']);
                $bot->sendMessage($message->getChat()->getId(), "О спикере: " . $result['about']);
                $bot->sendMessage($message->getChat()->getId(), "-----------------------------------");

            }
        }

    }, function ($message) use ($name){
        return true;
    });

    $bot->run();



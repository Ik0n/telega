<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 25.10.2017
 * Time: 18:17
 */

header('Content-Type: text/html; charset=utf-8');

require_once('vendor/autoload.php');
require_once('TelegramBot.php');
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$tb = new TelegramBot();


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
        $text = "Вас приветствуют “Digital Technologies Forum”!\nНаш бот поможет вам быть в курсе последних новостей Форума, формировать свою программу посещения мероприятий, голосовать за понравившиеся сессии.\nТакже мы всегда на связи, чтобы ответить на ваши вопросы.";
        $answer = 'Что я могу для вас сделать?';
        $board = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
            [
                [
                    ['url' => 'http://www.b2bcg.ru/events/digital-technology-forum/', 'text' => 'Офицальный сайт форума'],

                ],
                [
                    ['url' => 'https://plan-b.agency/', 'text' => 'Digital-партнер Plan B Agency'],
                ]
            ]
        );


        $db = pg_connect(pg_connection_string());
        $result = pg_query($db , "SELECT telegram_id FROM public.\"Users\" WHERE telegram_id = " . $message->getFrom()->getId() . ";");
        $result = pg_fetch_all($result);

        if ($result == null) {
            pg_query($db, "INSERT INTO public.\"Users\"(telegram_id) VALUES (" . $message->getFrom()->getId() . ");");
        }


        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
            [["text" => "Расписание"], ["text" => "Моё расписание"]],
            [["text" => "Лидеры голосования"]],
            [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
            [["text" => "Связаться с организаторами"]],
            [["text" => "О форуме"]],
        ], true, true);
        $bot->sendMessage($message->getChat()->getId(), $text, "HTML", null, null, $board);
        $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
    });

    $bot->command('help', function ($message) use ($bot) {
        $answer = 'Команды:
        /help - помощь';
        $bot->sendMessage($message->getChat()->getId(), $answer);
    });
   $bot->on(function ($update) use ($bot, $callback_loc, $find_command) {
      $callback = $update->getCallbackQuery();
      $message = $callback->getMessage();
      $chatId = $message->getChat()->getId();
      $fromId = $message->getFrom()->getId();
      $data = $callback->getData();
      $test = $update->getMessage();

      if ($data == stristr($data, "like")) {
          $db = pg_connect(pg_connection_string());
          $resultsUsers = pg_query($db, "SELECT id, telegram_id FROM public.\"Users\" WHERE telegram_id =". $chatId . ";");
          $resultsUsers = pg_fetch_all($resultsUsers);

          foreach ($resultsUsers as $result) {
              $resultsUserVoices = pg_query($db, "SELECT id, user_id, speaker_id FROM public.\"UserVoices\" WHERE user_id=" . $result['id'] . " and speaker_id=" . preg_replace("/[^0-9]/",'', $data) . ";");
              $resultsUserVoices = pg_fetch_all($resultsUserVoices);
              if ($resultsUserVoices == null) {
                  pg_query($db, "INSERT INTO public.\"UserVoices\"(user_id, speaker_id) VALUES (" . $result['id'] . "," . preg_replace("/[^0-9]/",'', $data) . ");");
                  $bot->answerCallbackQuery($callback->getId(), "Вы поставили отметку мне нравиться", true);
              } else {
                  $bot->answerCallbackQuery($callback->getId(), "Вы уже оценили данного спикера", true);
              }
          }
      }

       if ($data == stristr($data, "subs")) {
           $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
               [["text" => "Расписание"], ["text" => "Моё расписание"]],
               [["text" => "Лидеры голосования"]],
               [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
               [["text" => "Связаться с организаторами"]],
               [["text" => "О форуме"]],
           ], true, true);

           $db = pg_connect(pg_connection_string());
           $feedback = explode(':', $data);

           $results =  pg_query($db, "UPDATE public.\"Subscribers\"
	SET fio='" . file_get_contents('fio.txt') . "'
	WHERE email='" . $feedback[1] . "';");

           file_put_contents('fio.txt', "");
           $bot->sendMessage($chatId, 'Спасибо!', false, null, null, $keyboard);
       }

      if ($data == stristr($data, "add")) {
          $db = pg_connect(pg_connection_string());
          $resultsUsers = pg_query($db, "SELECT id, telegram_id FROM public.\"Users\" WHERE telegram_id =". $chatId . ";");
          $resultsUsers = pg_fetch_all($resultsUsers);



          foreach ($resultsUsers as $result) {
              $resultsMySchedule = pg_query($db, "SELECT id, user_id, schedule_id FROM public.\"MySchedule\" WHERE user_id=". $result['id'] . " and schedule_id=". preg_replace("/[^0-9]/",'', $data) .";");
              $resultsMySchedule = pg_fetch_all($resultsMySchedule);
              if ($resultsMySchedule == null) {
                  $bot->answerCallbackQuery($callback->getId(), "Данное мероприятие было добавлено в ваш список", true);
                  pg_query($db, "INSERT INTO public.\"MySchedule\" (user_id, schedule_id) VALUES (" . $result['id'] . "," . preg_replace("/[^0-9]/",'', $data) . ");");
              } else {
                  $bot->answerCallbackQuery($callback->getId(), "Данное мероприятие уже добавлено в ваш список.", true);
              }
          }
      }

      if ($data == stristr($data, "delete")) {
          $db = pg_connect(pg_connection_string());
          $resultsUsers = pg_query($db, "SELECT id, telegram_id FROM public.\"Users\" WHERE telegram_id =". $chatId . ";");
          $resultsUsers = pg_fetch_all($resultsUsers);

          foreach ($resultsUsers as $result) {
              pg_query($db, "DELETE FROM public.\"MySchedule\" WHERE user_id=" . $result['id'] . "and schedule_id=" . preg_replace("/[^0-9]/",'', $data) . ";");

              $bot->answerCallbackQuery($callback->getId(), "Это мероприятие удалено из вашего списка ", true);
          }
          $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
              [["text" => "Расписание"], ["text" => "Моё расписание"]],
              [["text" => "Лидеры голосования"]],
              [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
              [["text" => "Связаться с организаторами"]],
              [["text" => "О форуме"]],
          ], true, true);


          $bot->sendMessage($chatId, "Что я могу для вас сделать?", false, null, null, $keyboard);
      }


   }, function ($update) use ($bot) {
       $callback = $update->getCallbackQuery();
       if (is_null($callback) || !strlen($callback->getData())) {
           $message = $update->getMessage();
           $messageText = $message->getText();
           $userId = $message->getFrom()->getId();
           $feedback = explode(':', $messageText);
           file_put_contents('fio.txt', $messageText);
           //$bot->sendMessage($message->getChat()->getId(), preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', "88005553535"));

           if ($messageText == "Размещение") {
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Чат для делегатов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"], ["text" => "Меню"]],
               ], true, true);

               $text = "Гостиница \"Тянь-Шань\", размещена на территории Парка Сколково. Номера имеют площадь 30 кв.м и оснащены всеми необходимыми атрибутами для бизнес-путешественника. В каждом номере есть сейф, гладильная доска и утюг, а также все необходимое для приготовления чая или кофе. Для удобства гостей в номерах есть мини-бар. Ванные комнаты оснащены удобными душевыми кабинами, фенами, банными и косметическими принадлежностями. Все номера для некурящих.";

               $bot->sendMessage($message->getChat()->getId(), $text, "HTML");
               $bot->sendPhoto($message->getChat()->getId(), "https://bottelegabot.herokuapp.com/images/hotel.jpg");
               $bot->sendMessage($message->getChat()->getId(), "Стоимость проживания - 7500руб./чел. + 880 завтрак чел./день.", false, null, null, $keyboard);

           }

           if ($messageText == "Чат для делегатов") {
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Чат для делегатов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"], ["text" => "Меню"]],
               ], true, true);
               $chatKeyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                   [
                       [
                           ["url" => "https://t.me/digitaltechnologiesforum", "text" => "Чат для делегатов"]
                       ]
                   ]
               );
               $bot->sendMessage($message->getChat()->getId(), "Нажмите на кнопку", false, null, null, $chatKeyboard);
               $bot->sendMessage($message->getChat()->getId(), 'Здесь содержится полезная информация о нашем форуме. Выберите раздел:', false, null, null, $keyboard);
           }

           if ($messageText == "Расписание") {
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "30 ноября"]],
                   [["text" => "1 декабря"]],
                   [["text" => "Меню"]],
               ], true, true);
               $answer = "Выберите дату:";
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
           }

           if ($messageText == "Самое интересное") {
               $db = pg_connect(pg_connection_string());
               $results = pg_query($db, "SELECT content
	FROM public.\"MostInteresting\" ORDER BY created_at ASC");
               $results = pg_fetch_all($results);
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Чат для делегатов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"], ["text" => "Меню"]],
               ], true, true);
               foreach ($results as $result) {
                   $bot->sendMessage($message->getChat()->getId(), $result['content'] . "\n", "HTML");
               }
               $bot->sendMessage($message->getChat()->getId(), "Выберите раздел:", false, null, null, $keyboard);
           }

           if ($messageText == "Связаться с организаторами") {
               $answer = "Контактный номер для связи с организатором: <b>+7(926)232-15-37</b> \n Введите свой номер телефона, название компании и ваше сообщение через двоеточие.\n (Пример: 88005553535: НазваниеКомпании: Сообщение)";
               $bot->sendMessage($message->getChat()->getId(), $answer, "HTML");
           }

           if (preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', $messageText)) {
               $db = pg_connect(pg_connection_string());
               pg_query($db, "INSERT INTO public.\"Feedback\"(\"number\", company_name , content) VALUES ('" . $feedback[0] . "','" . $feedback[1] . "','" . $feedback[2] . "');");
               $subject = $feedback[0] . " " . $feedback[1];
               $mailMessage = "Номер телфона: " . $feedback[0] . ", Сообщение: " . $feedback[2];

               $mail = new PHPMailer;
               $mail->isSMTP();
               $mail->SMTPAuth = true;
               $mail->SMTPSecure = 'tls';
               $mail->Host = 'smtp.gmail.com';
               $mail->Port = '587';
               $mail->Username = "DigitalTechnologiesForum@gmail.com";
               $mail->Password = "digitaltechnologiesforum2018";
               $mail->CharSet = "UTF-8";
               $mail->setFrom("DigitalTechnologiesForum@gmail.com");

               $mail->Subject = $subject;
               $mail->Body = $mailMessage;
               $mail->addAddress('DigitalTechnologiesForum@gmail.com');

               $mail->send();

               $bot->sendMessage($message->getChat()->getId(), "Мы обязательно с вами свяжемся");
           }

           if ($messageText == "Подписаться на новости") {
               $answer = "Введите свой email";
               $bot->sendMessage($message->getChat()->getId(), $answer);
           }

           if (filter_var($messageText, FILTER_VALIDATE_EMAIL)) {
               $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                   [
                       [
                           ['callback_data' => 'subs:' . $messageText, 'text' => 'Готово']
                       ]
                   ]
               );
               $db = pg_connect(pg_connection_string());
               pg_query($db, "INSERT INTO public.\"Subscribers\" (email) VALUES ('" . $messageText . "');");
               $bot->sendMessage($message->getChat()->getId(), "Введите своё ФИО " . $messageText, false, null, null, $keyboard);
           }

           if ($messageText == "Лидеры голосования") {
               $db = pg_connect(pg_connection_string());
               $resultsUser = pg_query($db, "SELECT id, telegram_id
	FROM public.\"Users\"
	WHERE telegram_id = " . $userId . ";");
               $resultsUser = pg_fetch_all($resultsUser);
               $resultsUserVoices = pg_query($db, "SELECT public.\"Speakers\".id, first_name, last_name, refphoto, count(speaker_id) as \"counter\"
	                                                      FROM public.\"Speakers\"
                                                          LEFT OUTER JOIN public.\"UserVoices\" on public.\"Speakers\".id = public.\"UserVoices\".speaker_id
                                                          GROUP BY public.\"Speakers\".id,first_name, last_name, refphoto
                                                          ORDER BY counter DESC, id ASC
                                                          LIMIT 6"
               );
               $resultsUserVoices = pg_fetch_all($resultsUserVoices);

               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                   [
                       [
                           ["text" => "Пoказать ещё"]
                       ],
                       [
                           ["text" => "Меню"]
                       ]
                   ], true, true
               );
               foreach ($resultsUserVoices as $resultUserVoices) {
                   $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $resultUserVoices['first_name'] . " " . $resultUserVoices['last_name']);
                   if ($resultUserVoices['refphoto'] != "") {
                       $bot->sendPhoto($message->getChat()->getId(), $resultUserVoices['refphoto']);
                   }
                   $bot->sendMessage($message->getChat()->getId(), "Количество отметок мне нравится: " . $resultUserVoices['counter'], false, null, null, $keyboard);
               }

               foreach ($resultsUser as $resultUser) {
                   $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id =" . $resultUser['id'] . " and name = 'leaders_counter';");
                   $resultsVariables = pg_fetch_all($resultsVariables);

                   if ($resultsVariables != null) {
                       foreach ($resultsVariables as $resultVariable) {
                           pg_query($db, "UPDATE public.\"Variables\"
	SET value='" . 6 . "'
	WHERE id =" . $resultVariable['id'] . ";");
                       }
                   } else {
                       pg_query($db, "INSERT INTO public.\"Variables\"(
	user_id, name, value)
	VALUES (" . $resultUser['id'] . ",'leaders_counter','" . 6 . "');");
                   }
               }

               //file_put_contents('counter.txt', 6);
           }

           if ($messageText == "Пoказать ещё") {
               $db = pg_connect(pg_connection_string());
               $resultsUser = pg_query($db, "SELECT id, telegram_id
	FROM public.\"Users\"
	WHERE telegram_id = " . $userId . ";");
               $resultsUser = pg_fetch_all($resultsUser);

               foreach ($resultsUser as $resultUser) {
                   $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id = " . $resultUser['id'] . " and name ='leaders_counter'");
                   $resultsVariables = pg_fetch_all($resultsVariables);

                   foreach ($resultsVariables as $resultVariable) {

                       $resultsUserVoices = pg_query($db, "SELECT public.\"Speakers\".id, first_name, last_name, refphoto, count(speaker_id) as \"counter\"
	                                                      FROM public.\"Speakers\"
                                                          LEFT OUTER JOIN public.\"UserVoices\" on public.\"Speakers\".id = public.\"UserVoices\".speaker_id
                                                          GROUP BY public.\"Speakers\".id,first_name, last_name, refphoto
                                                          ORDER BY counter DESC, id ASC
                                                          LIMIT 6 OFFSET " . $resultVariable['value']
                       );
                       $resultsUserVoices = pg_fetch_all($resultsUserVoices);

                       if ($resultsUserVoices != null) {
                           $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                               [
                                   [
                                       ["text" => "Пoказать ещё"]
                                   ],
                                   [
                                       ["text" => "Меню"]
                                   ]
                               ], true, true
                           );

                           foreach ($resultsUserVoices as $resultUserVoices) {
                               $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $resultUserVoices['first_name'] . " " . $resultUserVoices['last_name']);
                               if ($resultUserVoices['refphoto'] != "") {
                                   $bot->sendPhoto($message->getChat()->getId(), $resultUserVoices['refphoto']);
                               }
                               $bot->sendMessage($message->getChat()->getId(), "Количество отметок мне нравится: " . $resultUserVoices['counter'], false, null, null, $keyboard);
                           }

                           foreach ($resultsUser as $resultUser) {
                               $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id = " . $resultUser['id'] . " and name ='leaders_counter'");
                               $resultsVariables = pg_fetch_all($resultsVariables);

                               foreach ($resultsVariables as $resultVariable) {
                                   pg_query($db, "UPDATE public.\"Variables\"
	SET value ='" . ($resultVariable['value'] + 6) . "'
	WHERE id = " . $resultVariable['id'] . ";");
                               }
                           }


                           //file_put_contents('counter.txt', file_get_contents('counter.txt') + 6);

                       } else {
                           $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                               [["text" => "Расписание"], ["text" => "Моё расписание"]],
                               [["text" => "Лидеры голосования"]],
                               [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
                               [["text" => "Связаться с организаторами"]],
                               [["text" => "О форуме"]],
                           ], true, true);

                           $bot->sendMessage($message->getChat()->getId(), "Вы просмотрели весь список спикеров! ", false, null, null, $keyboard);


                           foreach ($resultsUser as $resultUser) {
                               $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id = " . $resultUser['id'] . " and name ='leaders_counter'");
                               $resultsVariables = pg_fetch_all($resultsVariables);

                               foreach ($resultsVariables as $resultVariable) {
                                   pg_query($db, "UPDATE public.\"Variables\"
	SET value = 0
	WHERE id = " . $resultVariable['id'] . ";");
                               }
                           }


                           //file_put_contents("counter.txt", 0);
                       }
                   }
               }
           }

           if ($messageText == "Меню") {
               $answer = 'Что я могу для вас сделать?';
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "Расписание"], ["text" => "Моё расписание"]],
                   [["text" => "Лидеры голосования"]],
                   [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
                   [["text" => "Связаться с организаторами"]],
                   [["text" => "О форуме"]],
               ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
           }

           if ($messageText == "О форуме") {
               $answer = 'Здесь содержится полезная информация о нашем форуме. Выберите раздел:';
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Чат для делегатов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"], ["text" => "Меню"]],
               ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
           }

           if ($messageText == "О Гиперкубе") {
               $answer = '«Гиперкуб» − это центр городского развития «Сколково». Центр, в котором разрабатываются информационные, экономические, инженерные,градостроительные и организационные модели будущего и царит атмосфера творчества, в которой реализуются любые идеи.';
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Чат для делегатов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"], ["text" => "Меню"]],
               ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
           }

           if ($messageText == "Как добраться") {
               $answer = 'Адрес: ИЦ Сколково, ул. Малевича, д 1. Москва';
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Чат для делегатов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"], ["text" => "Меню"]],
               ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
               //$bot->sendLocation($message->getChat()->getId(), 55.696560, 37.355938);
               //$bot->sendPhoto($message->getChat()->getId(), "https://wmpics.pics/di-4HCA.jpg");
               //$bot->sendMessage($message->getChat()->getId(), "Расписание движения транспорта по внутренним маршрутам");
               //$bot->sendPhoto($message->getChat()->getId(), "http://sk.ru/cfs-file.ashx/__key/communityserver-components-userfiles/00-00-02-15-95-_3F044004380441043E043504340438043D0435043D043D044B043504_+_4404300439043B044B04_/aaaaaaa.png");
               //$bot->sendMessage($message->getChat()->getId(), "Внутренние маршруты");
               //$bot->sendPhoto($message->getChat()->getId(), "http://sk.ru/cfs-file.ashx/__key/communityserver-components-userfiles/00-00-02-15-95-_3F044004380441043E043504340438043D0435043D043D044B043504_+_4404300439043B044B04_/scheme2_2D00_inner_2D00_5_2D00_ru.jpg");
               //$bot->sendMessage($message->getChat()->getId(), "На общественном транспорте");
               //$bot->sendPhoto($message->getChat()->getId(), "http://sk.ru/cfs-file.ashx/__key/communityserver-components-userfiles/00-00-02-15-95-attached+files/scheme2_2D00_outer_2D00_4_2D00_ru.jpg");
               $bot->sendMessage($message->getChat()->getId(), "Расписание трасферов");
               $bot->sendPhoto($message->getChat()->getId(), "https://wmpics.pics/di-6CYJ.png");
               $bot->sendMessage($message->getChat()->getId(), "На автомобиле");
               $bot->sendPhoto($message->getChat()->getId(), "https://wmpics.pics/di-4HCA.jpg");

           }


           if ($messageText == "Спикеры") {
               $db = pg_connect(pg_connection_string());
               $resultsUser = pg_query($db, "SELECT id, telegram_id
	FROM public.\"Users\"
	WHERE telegram_id = " . $userId . ";");
               $resultsUser = pg_fetch_all($resultsUser);
               $results = pg_query($db, "SELECT id, first_name, last_name, about, refphoto, session FROM public.\"Speakers\" ORDER BY last_name LIMIT 6;");
               $results = pg_fetch_all($results);
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                   [
                       [
                           ["text" => "Показать ещё"]
                       ],
                       [
                           ["text" => "Меню"]
                       ]
                   ], true, true
               );

               foreach ($results as $result) {
                   $likeKeyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       [
                           [
                               ["callback_data" => "like" . $result['id'], "text" => "Мне нравится"]
                           ]
                       ]
                   );


                   $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $result['first_name'] . " " . $result['last_name']);
                   if ($result['refphoto'] != "") {
                       $bot->sendPhoto($message->getChat()->getId(), $result['refphoto']);
                   }
                   $bot->sendMessage($message->getChat()->getId(), "О спикере: " . $result['about']);
                   $bot->sendMessage($message->getChat()->getId(), "Сессия: " . $result['session'], false, null, null, $likeKeyboard);
                   //$bot->sendMessage($message->getChat()->getId(), "-----------------------------------");
               }

               foreach ($resultsUser as $resultUser) {
                   $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id =" . $resultUser['id'] . " and name = 'speaker_counter';");
                   $resultsVariables = pg_fetch_all($resultsVariables);

                   if ($resultsVariables != null) {
                       foreach ($resultsVariables as $resultVariable) {
                           pg_query($db, "UPDATE public.\"Variables\"
	SET value='" . 6 . "'
	WHERE id =" . $resultVariable['id'] . ";");
                       }
                   } else {
                       pg_query($db, "INSERT INTO public.\"Variables\"(
	user_id, name, value)
	VALUES (" . $resultUser['id'] . ",'speaker_counter','" . 6 . "');");
                   }
               }

           //file_put_contents("counter.txt", 6);
           $bot->sendMessage($message->getChat()->getId(), "Выберите действие ", false, null, null, $keyboard);

       }

       if ($messageText == "Показать ещё") {
           $db = pg_connect(pg_connection_string());
           $resultsUser = pg_query($db, "SELECT id, telegram_id
	FROM public.\"Users\"
	WHERE telegram_id = " . $userId . ";");
           $resultsUser = pg_fetch_all($resultsUser);

           foreach ($resultsUser as $resultUser) {
               $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id = " . $resultUser['id'] . " and name ='speaker_counter'");
               $resultsVariables = pg_fetch_all($resultsVariables);

               foreach ($resultsVariables as $resultVariable) {

                   $results = pg_query($db, "SELECT id, first_name, last_name, about, refphoto, session
	FROM public.\"Speakers\"
    ORDER BY last_name
    LIMIT 6 OFFSET " . $resultVariable['value'] . ";");
                   $results = pg_fetch_all($results);

                   if ($results != null) {
                       $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                           [
                               [
                                   ["text" => "Показать ещё"]
                               ],
                               [
                                   ["text" => "Меню"]
                               ]
                           ], true, true
                       );

                       foreach ($results as $result) {
                           $likeKeyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                               [
                                   [
                                       ["callback_data" => "like" . $result['id'], "text" => "Мне нравится"]
                                   ]
                               ]
                           );

                           $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $result['first_name'] . " " . $result['last_name']);
                           if ($result['refphoto'] != "") {
                               $bot->sendPhoto($message->getChat()->getId(), $result['refphoto']);
                           }
                           $bot->sendMessage($message->getChat()->getId(), "О спикере: " . $result['about']);
                           $bot->sendMessage($message->getChat()->getId(), "Сессия: " . $result['session'], false, null, null, $likeKeyboard);
                       }

                       foreach ($resultsUser as $resultUser) {
                           $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id = " . $resultUser['id'] . " and name ='speaker_counter'");
                           $resultsVariables = pg_fetch_all($resultsVariables);

                           foreach ($resultsVariables as $resultVariable) {
                               pg_query($db, "UPDATE public.\"Variables\"
	SET value ='" . ($resultVariable['value'] + 6) . "'
	WHERE id = " . $resultVariable['id'] . ";");
                           }
                       }


                       $bot->sendMessage($message->getChat()->getId(), "Выберите действие", false, null, null, $keyboard);
                       //file_put_contents("counter.txt", file_get_contents("counter.txt") + 6);
                   } else {
                       $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                           [["text" => "Расписание"], ["text" => "Моё расписание"]],
                           [["text" => "Лидеры голосования"]],
                           [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
                           [["text" => "Связаться с организаторами"]],
                           [["text" => "О форуме"]],
                       ], true, true);

                       $bot->sendMessage($message->getChat()->getId(), "Вы просмотрели весь список спикеров! ", false, null, null, $keyboard);

                       foreach ($resultsUser as $resultUser) {
                           $resultsVariables = pg_query($db, "SELECT id, user_id, name, value
	FROM public.\"Variables\"
    WHERE user_id = " . $resultUser['id'] . " and name ='speaker_counter'");
                           $resultsVariables = pg_fetch_all($resultsVariables);

                           foreach ($resultsVariables as $resultVariable) {
                               pg_query($db, "UPDATE public.\"Variables\"
	SET value = 0
	WHERE id = " . $resultVariable['id'] . ";");
                           }
                       }

                       //file_put_contents("counter.txt", 0);
                   }

               }
           }

       }

       if ($messageText == "30 ноября") {
           $db = pg_connect(pg_connection_string());
           $results = pg_query($db, "SELECT id, title, date_begin, date_end, time_begin, time_end FROM public.\"Schedule\" WHERE date_begin like '30%' ORDER BY time_begin;");
           $results = pg_fetch_all($results);
           foreach ($results as $result) {
               $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                   [
                       [
                           ['callback_data' => "add" . $result['id'], 'text' => 'Добавить в своё расписание ']
                       ]
                   ]
               );
               $time_begin = explode(':', $result['time_begin']);
               $time_end = explode(":", $result['time_end']);
               $bot->sendMessage($message->getChat()->getId(), "<b>Тема(ы): </b>" . $result['title'] . "\n" .
                   "<b>Начало: </b>" . $result['date_begin'] . ", " . $time_begin[0] . ":" . $time_begin[1] . "\n" .
                   "<b>Завершение: </b>" . $result['date_end'] . ", " . $time_end[0] . ":" . $time_end[1], "HTML", null, null, $keyboard);
           }
           $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
               [["text" => "30 ноября"]],
               [["text" => "1 декабря"]],
               [["text" => "Меню"]],
           ], true, true);

           $bot->sendMessage($message->getChat()->getId(), "Выберите дату: ", false, null, null, $keyboard);
       }

       if ($messageText == "1 декабря") {
           $db = pg_connect(pg_connection_string());
           $results = pg_query($db, "SELECT id, title, date_begin, date_end, time_begin, time_end FROM public.\"Schedule\" WHERE date_begin like '1%' ORDER BY time_begin;");
           $results = pg_fetch_all($results);
           $date = "1";
           foreach ($results as $result) {
               $string = stristr($result['begin'], $date);
               if ($string == $result['begin']) {
                   $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       [
                           [
                               ['callback_data' => "add" . $result['id'], 'text' => 'Добавить в своё расписание']
                           ]
                       ]
                   );
                   $time_begin = explode(':', $result['time_begin']);
                   $time_end = explode(":", $result['time_end']);
                   $bot->sendMessage($message->getChat()->getId(), "<b>Тема(ы): </b>" . $result['title'] . "\n" .
                       "<b>Начало: </b>" . $result['date_begin'] . ", " . $time_begin[0] . ":" . $time_begin[1] . "\n" .
                       "<b>Завершение: </b>" . $result['date_end'] . ", " . $time_end[0] . ":" . $time_end[1], "HTML", null, null, $keyboard);
               }
           }
           $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
               [["text" => "30 ноября"]],
               [["text" => "1 декабря"]],
               [["text" => "Меню"]],
           ], true, true);

           $bot->sendMessage($message->getChat()->getId(), "Выберите дату: ", false, null, null, $keyboard);
       }

       if ($messageText == "Моё расписание") {
           $mainKeyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
               [["text" => "Расписание"], ["text" => "Моё расписание"]],
               [["text" => "Лидеры голосования"]],
               [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
               [["text" => "Связаться с организаторами"]],
               [["text" => "О форуме"]],
           ], true, true);

           $db = pg_connect(pg_connection_string());
           $results = pg_query($db, "SELECT public.\"Users\".id, public.\"Schedule\".id as schedule_id, public.\"Schedule\".title, public.\"Schedule\".date_begin, public.\"Schedule\".date_end, public.\"Schedule\".time_begin, public.\"Schedule\".time_end
	                                            FROM public.\"Users\"
                                                JOIN public.\"MySchedule\" on public.\"Users\".id = public.\"MySchedule\".user_id
                                                JOIN public.\"Schedule\" on public.\"MySchedule\".schedule_id = public.\"Schedule\".id
                                                WHERE public.\"Users\".telegram_id =" . $userId);
           $results = pg_fetch_all($results);
           $bot->sendMessage($message->getChat()->getId(), "Ваше расписание :");
           foreach ($results as $result) {
               $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                   [
                       [
                           ['callback_data' => "delete" . $result['schedule_id'], 'text' => 'Удалить из своего расписания ']
                       ]
                   ]
               );
               $time_begin = explode(':', $result['time_begin']);
               $time_end = explode(":", $result['time_end']);
               $bot->sendMessage($message->getChat()->getId(), "<b>Тема(ы): </b>" . $result['title'] . "\n" .
                   "<b>Начало: </b>" . $result['date_begin'] . ", " . $time_begin[0] . ":" . $time_begin[1] . "\n" .
                   "<b>Завершение: </b>" . $result['date_end'] . ", " . $time_end[0] . ":" . $time_end[1], "HTML", null, null, $keyboard);
               $bot->sendMessage($message->getChat()->getId(), "Что я могу для вас сделать?", false, null, null, $mainKeyboard);
           }
       }

       return false;
   }


       return true;
   });

    $bot->run();


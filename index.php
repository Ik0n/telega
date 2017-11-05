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
        $answer = 'Что я могу для вас сделать?';

        $db = pg_connect(pg_connection_string());
        $result = pg_query($db , "SELECT telegram_id FROM public.\"Users\" WHERE telegram_id = " . $message->getFrom()->getId() . ";");
        $result = pg_fetch_all($result);

        if ($result == null) {
            pg_query($db, "INSERT INTO public.\"Users\"(telegram_id) VALUES (" . $message->getFrom()->getId() . ");");
        }


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
   $bot->on(function ($update) use ($bot, $callback_loc, $find_command) {
      $callback = $update->getCallbackQuery();
      $message = $callback->getMessage();
      $chatId = $message->getChat()->getId();
      $fromId = $message->getFrom()->getId();
      $data = $callback->getData();

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
          $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
              [
                  [["text" => "Моё расписание"]]
              ], true, true
          );


          $bot->sendMessage($chatId, "Нажмите для обновления списка", false, null, null, $keyboard);
      }


   }, function ($update) use ($bot){
       $callback = $update->getCallbackQuery();
       if (is_null($callback) || !strlen($callback->getData())) {
        $message = $update->getMessage();
        $messageText = $message->getText();
        $userId = $message->getFrom()->getId();
        $feedback = explode(':', $messageText);
        global $tb;
        //$bot->sendMessage($message->getChat()->getId(), preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', "88005553535"));

        if($messageText == "Расписание") {
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "30 ноября"]],
                   [["text" => "1 декабря"]],
                   [["text" => "Меню"]],
               ], true, true);
               $answer = "Выберите дату:";
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "Связаться с организаторами") {
            $answer = "Контактный номер для связи с организатором: <b>+7(926)232-15-37</b> \n Введите свой номер телефона, название компании и ваше сообщение через двоеточие.\n (Пример: 88005553535: НазваниеКомпании: Сообщение)";
            $bot->sendMessage($message->getChat()->getId(), $answer, "HTML");
        }

        if (preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', $messageText)) {
            $db = pg_connect(pg_connection_string());
            pg_query($db, "INSERT INTO public.\"Feedback\"(\"number\", company_name , content) VALUES ('" . $feedback[0] . "','" . $feedback[1] . "','" . $feedback[2] . "');");
            $subject = $feedback[0] . " " . $feedback[1];
            $mailMessage = $feedback[2];

            $mail = new PHPMailer;
            //$mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = '587';
            $mail->Username = "ik0n16111998@gmail.com";
            $mail->Password = "MegaForever40";
            $mail->setFrom("ik0n16111998@gmail.com");

            $mail->Subject = $subject;
            $mail->Body = $mailMessage;
            $mail->addAddress('ik0n16111998@gmail.com');

            $mail->send();

            $bot->sendMessage($message->getChat()->getId(), "Мы обязательно с вами свяжемся");
        }

        if ($messageText == "Подписаться на новости") {
            $answer = "Введите свой email";
            $bot->sendMessage($message->getChat()->getId(), $answer);
        }

        if (filter_var($messageText, FILTER_VALIDATE_EMAIL)) {
            $db = pg_connect(pg_connection_string());
            pg_query($db, "INSERT INTO public.\"Subscribers\" (email) VALUES ('" . $messageText . "');");
            $bot->sendMessage($message->getChat()->getId(), "Вы попдисались на новости " . $messageText);
        }

        if ($messageText == "Лидеры голосования") {
            $db = pg_connect(pg_connection_string());
            $resultsUserVoices = pg_query($db, "SELECT name, refphoto, count(speaker_id) as \"counter\"
	                                                      FROM public.\"Speakers\"
                                                          LEFT OUTER JOIN public.\"UserVoices\" on public.\"Speakers\".id = public.\"UserVoices\".speaker_id
                                                          GROUP BY name, refphoto
                                                          ORDER BY counter DESC");
            $resultsUserVoices = pg_fetch_all($resultsUserVoices);


            foreach ($resultsUserVoices as $resultUserVoices) {
                $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $resultUserVoices['name']);
                $bot->sendPhoto($message->getChat()->getId(), $resultUserVoices['refphoto']);
                $bot->sendMessage($message->getChat()->getId(), "Количество отметок мне нравиться: " . $resultUserVoices['counter']);
            }
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
               $answer = 'Здесь содержится полезная информация о нашем форуме. Выберите раздел:';
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "О Гиперкубе"]],
                   [["text" => "Биржа деловых контактов"]],
                   [["text" => "Самое интересное"]],
                   [["text" => "Как добраться"]],
                   [["text" => "Размещение"],["text" => "Меню"]],
               ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "О Гиперкубе") {
               $answer = '«Гиперкуб» − это центр городского развития «Сколково». Центр, в котором разрабатываются информационные, экономические, инженерные,градостроительные и организационные модели будущего и царит атмосфера творчества, в которой реализуются любые идеи.';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "О Гиперкубе"]],
                [["text" => "Биржа деловых контактов"]],
                [["text" => "Самое интересное"]],
                [["text" => "Как добраться"]],
                [["text" => "Размещение"],["text" => "Меню"]],
            ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }

        if ($messageText == "Как добраться") {
               $answer = 'Адрес: ИЦ Сколково, ул. Малевича, д 1. Москва';
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                [["text" => "О Гиперкубе"]],
                [["text" => "Биржа деловых контактов"]],
                [["text" => "Самое интересное"]],
                [["text" => "Как добраться"]],
                [["text" => "Размещение"],["text" => "Меню"]],
            ], true, true);
               $bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboard);
        }



        if ($messageText == "Спикеры") {
               $db = pg_connect(pg_connection_string());
               $results = pg_query($db, "SELECT id, name, about, refphoto, session FROM public.\"Speakers\" ORDER BY id LIMIT 6;");
               $results = pg_fetch_all($results);
                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                    [
                        [
                            ["text" => "Показать ещё"]
                        ]
                    ], true, true
                );

               foreach ($results as $result) {
                   $likeKeyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       [
                           [
                               ["callback_data" => "like" . $result['id'], "text" => "Мне нравиться"]
                           ]
                       ]
                   );



                   $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $result['name']);
                   $bot->sendPhoto($message->getChat()->getId(), $result['refphoto']);
                   $bot->sendMessage($message->getChat()->getId(), "О спикере: " . $result['about']);
                   $bot->sendMessage($message->getChat()->getId(), "Сессия: " . $result['session'], false, null, null, $likeKeyboard);
                   //$bot->sendMessage($message->getChat()->getId(), "-----------------------------------");
               }
                file_put_contents("counter.txt", 6);
                $bot->sendMessage($message->getChat()->getId(), "Выберите действие ", false, null, null, $keyboard);

        }

        if ($messageText == "Показать ещё") {
            $db = pg_connect(pg_connection_string());
            $results = pg_query($db, "SELECT id, name, about, refphoto, session
	FROM public.\"Speakers\"
    ORDER BY id
    LIMIT 6 OFFSET " . file_get_contents("counter.txt") . ";");
            $results = pg_fetch_all($results);

            if ($results != null) {
                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                    [
                        [
                            ["text" => "Показать ещё"]
                        ]
                    ], true, true
                );

                foreach ($results as $result) {
                    $likeKeyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                            [
                                ["callback_data" => "like" . $result['id'], "text" => "Мне нравиться"]
                            ]
                        ]
                    );

                    $bot->sendMessage($message->getChat()->getId(), "Спикер: " . $result['name']);
                    $bot->sendPhoto($message->getChat()->getId(), $result['refphoto']);
                    $bot->sendMessage($message->getChat()->getId(), "О спикере: " . $result['about']);
                    $bot->sendMessage($message->getChat()->getId(), "Сессия: " . $result['session'], false, null, null, $likeKeyboard);
                }

                $bot->sendMessage($message->getChat()->getId(), "Выберите действие", false, null, null, $keyboard);
                file_put_contents("counter.txt", file_get_contents("counter.txt") + 6);
            } else {
                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                    [["text" => "Расписание"], ["text" => "Моё расписание"]],
                    [["text" => "Оценить доклад"], ["text" => "Лидеры голосования"]],
                    [["text" => "Спикеры"], ["text" => "Подписаться на новости"]],
                    [["text" => "Связаться с организаторами"]],
                    [["text" => "О форуме"]],
                ], true, true);

                $bot->sendMessage($message->getChat()->getId(), "Вы просмотрели весь список спикеров! ", false, null, null, $keyboard);
                file_put_contents("counter.txt", 0);
            }

        }

           if ($messageText == "30 ноября") {
               $db = pg_connect(pg_connection_string());
               $results = pg_query($db, "SELECT id ,title, begin, \"end\" FROM public.\"Schedule\" WHERE begin like '30%';");
               $results = pg_fetch_all($results);
               foreach ($results as $result) {
                   $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       [
                           [
                               ['callback_data' => "add" . $result['id'], 'text' => 'Добавить в своё расписание ']
                           ]
                       ]
                   );
                   $bot->sendMessage($message->getChat()->getId(), "<b>Тема(ы): </b>" . $result['title'] . "\n" .
                       "<b>Дата и время начала: </b>" . $result['begin'] . "\n" .
                       "<b>Дата и время конца: </b>" . $result['end'] . "", "HTML" , null, null, $keyboard);
               }
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "30 ноября"]],
                   [["text" => "1 декабря"]],
                   [["text" => "Меню"]],
               ], true, true);

               $bot->sendMessage($message->getChat()->getId(), "Выбирите дату: ", false, null, null, $keyboard);
           }

           if ($messageText == "1 декабря") {
               $db = pg_connect(pg_connection_string());
               $results = pg_query($db, "SELECT id ,title, begin, \"end\" FROM public.\"Schedule\" WHERE begin like '1%';");
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
                       $bot->sendMessage($message->getChat()->getId(), "<b>Тема(ы): </b>" . $result['title'] . "\n" .
                           "<b>Дата и время начала: </b>" . $result['begin'] . "\n" .
                           "<b>Дата и время конца: </b>" . $result['end'] . "", "HTML" , null, null, $keyboard);
                   }
               }
               $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                   [["text" => "30 ноября"]],
                   [["text" => "1 декабря"]],
                   [["text" => "Меню"]],
               ], true, true);

               $bot->sendMessage($message->getChat()->getId(), "Выбирите дату: ", false, null, null, $keyboard);
           }

           if ($messageText == "Моё расписание") {
               $db = pg_connect(pg_connection_string());
               $results = pg_query($db, "SELECT public.\"Users\".id, public.\"Schedule\".id as schedule_id, public.\"Schedule\".title, public.\"Schedule\".begin, public.\"Schedule\".end
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
                   $bot->sendMessage($message->getChat()->getId(), "<b>Тема(ы): </b>" . $result['title'] . "\n" .
                       "<b>Дата и время начала: </b>" . $result['begin'] . "\n" .
                       "<b>Дата и время конца: </b>" . $result['end'] . "", "HTML" , null, null, $keyboard);
               }
           }

        return false;
       }
       return true;
   });

    $bot->run();


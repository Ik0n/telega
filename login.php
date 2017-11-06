<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 01.11.2017
 * Time: 15:31
 */

if ($_POST['login'] == "admin" && $_POST['password'] == "admin") {
    $test = date("H:i", time() + 10800);

    echo "Вы вошли <br>";
    echo "<a href='/speakers/'>Спикеры</a><br>";
    echo "<a href='/schedule/'>Расписание</a><br>";
    echo "<a href='/subscribers/'>Подписчики</a><br>";
    echo "<a href='/feedback/'>Заявки для обратной связи</a><br>";
    echo "<a href='/MostInteresting/'>Самое интересное</a><br>";
} else {
    header('Location: /login.html', true, 301);
}

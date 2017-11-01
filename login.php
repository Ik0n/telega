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
    echo "<a href='speakers/index.php'>Спикеры</a><br>";
    echo "<a href='schedule/index.php'>Расписание</a>";
    echo "<a href='subscribers/index.php'>Подписчики</a>";
    echo "<a href='feedback/index.php'>Самое интересное</a>";
} else {
    header('Location: login.html', true, 301);
}

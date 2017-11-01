<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 01.11.2017
 * Time: 15:31
 */

if ($_POST['login'] == "admin" && $_POST['password'] == "admin") {
    $test = date("H:i", time() + 10800);
    var_dump(date("Y-m-d") . " " . $test);
    echo "Вы вошли <br>";
    echo "<a href='speakers/index.php'>Спикеры</a>";
    echo "<a href='schedule/index.php'>Расписание</a>";
} else {
    header('Location: login.html', true, 301);
}

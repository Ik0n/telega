<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 01.11.2017
 * Time: 15:31
 */

if ($_POST['login'] == "admin" && $_POST['password'] == "admin") {
    setcookie("user", "admin");
    echo "Вы вошли <br>";
    echo "<a href='speakers/index.php'>Спикеры</a>";
} else {
    header('Location: login.html', true, 301);
}

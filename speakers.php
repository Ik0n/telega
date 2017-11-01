<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 01.11.2017
 * Time: 16:23
 */
    $db = pg_connect(pg_connection_string());
    $results = pg_query($db, "SELECT id, name, about, refphoto, session FROM public.\"Speakers\";");
    $results = pg_fetch_all($results);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Adminka</title>
</head>
<body>
    <? foreach ($results as $result) { ?>
        <table>
            <tr>
                <td><? $result['id'] ?></td>
                <td><? $result['name'] ?></td>
                <td><? $result['about'] ?></td>
                <td><? $result['refphoto'] ?></td>
                <td><? $result['session'] ?></td>
            </tr>
        </table>
    <? } ?>
    <form action="speakers.php" method="POST">
        ФИО спикера
        <br>
        <input type="text" name="name" title="name">
        <hr>
        О спикере
        <br>
        <input type="textarea" rows="10" cols="45" name="about" title="about">
        <hr>
        Ссылка на фотографию спикера
        <br>
        <input type="text" name="refphoto" title="refphoto">
        <hr>
        Сессия в которой участвует спикер
        <br>
        <input type="text" name="session" title="session">
        <hr>
        <input type="submit">
    </form>
</body>
</html>



<?php
/*
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 01.11.2017
 * Time: 16:23
 */
    $db = pg_connect("dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require");

    if (isset($_POST['submit'])) {
        $data = $_POST;
        //if ($data['refphoto'] == null) {
        //    $uploaddir = '/images/';
        //    $uploadfile = $uploaddir . $_FILES['filename']['name'];
        //    $fileTempName = $_FILES['filename']['tmp_name'];
        //
        //    move_uploaded_file($fileTempName, $uploadfile);
        //    pg_query($db, "INSERT INTO public.\"Speakers\"(name, about, refphoto, session) VALUES ('". $data['name'] ."','" . $data['about'] ."','" . "https://bottelegabot.herokuapp.com/images/" . $_FILES['filename']['name'] . "','" . $data['session'] . "');");
        //} else {
            pg_query($db, "INSERT INTO public.\"Speakers\"(first_name, last_name, about, refphoto, session) VALUES ('". $data['first_name'] ."','" . $data['last_name'] . "','" . $data['about'] ."','" . $data['refphoto'] . "','" . $data['session'] . "');");
        //}
    }

    $results = pg_query($db, "SELECT id, first_name, last_name, about, refphoto, session FROM public.\"Speakers\" ORDER BY id;");
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
<?
echo "<a href='/speakers/'>Спикеры</a><br>";
echo "<a href='/schedule/'>Расписание</a><br>";
echo "<a href='/subscribers/'>Подписчики</a><br>";
echo "<a href='/feedback/'>Заявки для обратной связи</a><br>";
echo "<a href='/MostInteresting/'>Самое интересное</a><br>"
?>

    <? foreach ($results as $result) { ?>
        <table border="1px solid black">
            <tr>
                <td><? echo $result['id']; ?></td>
                <td><? echo $result['first_name']; ?></td>
                <td><? echo $result['last_name']; ?></td>
                <td><? echo $result['about']; ?></td>
                <td><? echo $result['refphoto']; ?></td>
                <td><? echo $result['session']; ?></td>
                <td><? echo "<a href='delete.php?id=" . $result['id']. "'>Удалить запись</a>"?></td>
                <td><? echo "<a href='edit.php?id=" . $result['id'] . "'>Изменить запись</a>"?></td>
            </tr>
        </table>
    <? } ?>
    <form action="index.php" method="POST">
        Фамилия спикера
        <br>
        <input type="text" name="last_name" title="last_name" required>
        <hr>
        Имя спикера
        <br>
        <input type="text" name="first_name" title="first_name" required>
        <hr>
        О спикере
        <br>
        <textarea name="about" id="about" title="about" cols="30" rows="10"></textarea>
        <hr>
        Ссылка на фотографию спикера
        <br>
        <input type="text" name="refphoto" title="refphoto">
        <hr>
        Сессия в которой участвует спикер
        <br>
        <input type="text" name="session" title="session" required>
        <hr>
        <input type="submit" name="submit">
    </form>
</body>
</html>



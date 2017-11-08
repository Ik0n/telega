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
    pg_query($db, "INSERT INTO public.\"Schedule\"(
	title, date_begin, date_end, time_begin, time_end)
	VALUES ('". $data['title'] ."','" . $data['date_begin'] . "','" . $data['date_end'] . "','" . $data['time_begin'] . "','" . $data['time_end'] . "');");

}

$results = pg_query($db, "SELECT id, title, date_begin, date_end, time_begin, time_end FROM public.\"Schedule\";");
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
            <td><? echo $result['title']; ?></td>
            <td><? echo $result['date_begin']; ?></td>
            <td><? echo $result['time_begin']; ?></td>
            <td><? echo $result['date_end']; ?></td>
            <td><? echo $result['time_end']; ?></td>
            <td><? echo "<a href='delete.php?id=" . $result['id']. "'>Удалить запись</a>"?></td>
            <td><? echo "<a href='edit.php?id=" . $result['id']. "'>Изменить запись</a>"?></td>
        </tr>
    </table>
<? } ?>
<form action="index.php" method="POST">
    Название мероприятия
    <br>
    <input type="text" name="title" title="title" required>
    <hr>
    Дата начала мероприятия(пример: 1 ноября 2017)
    <br>
    <input type="text" name="date_begin" title="date_begin" required>
    <hr>
    Время начала мероприятия(пример: 09:30)
    <br>
    <input type="text" name="time_begin" title="time_begin" required>
    <hr>
    Дата конца мероприятия(пример: 1 ноября 2017)
    <br>
    <input type="text" name="date_end" title="date_end" required>
    <hr>
    Время конца мероприятия(пример: 09:30)
    <br>
    <input type="text" name="time_end" title="time_end" required>
    <hr>
    <input type="submit" name="submit">
</form>
</body>
</html>



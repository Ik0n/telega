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
	title, begin, \"end\")
	VALUES ('". $_POST['title'] ."','" . $_POST['begin'] . "','" . $_POST['end'] . "');");

}

$results = pg_query($db, "SELECT id, title, begin, \"end\" FROM public.\"Schedule\";");
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
echo "<a href='/speakers/index.php'>Спикеры</a><br>";
echo "<a href='/schedule/index.php'>Расписание</a><br>";
echo "<a href='/subscribers/index.php'>Подписчики</a><br>";
echo "<a href='/feedback/index.php'>Самое интересное</a><br>";
?>

<? foreach ($results as $result) { ?>
    <table border="0.5px solid black">
        <tr>
            <td><? echo $result['id']; ?></td>
            <td><? echo $result['title']; ?></td>
            <td><? echo $result['begin']; ?></td>
            <td><? echo $result['end']; ?></td>
            <td><? echo "<a href='delete.php?id=" . $result['id']. "'>Удалить запись</a>"?></td>
        </tr>
    </table>
<? } ?>
<form action="index.php" method="POST">
    Название мероприятия
    <br>
    <input type="text" name="title" title="title" required>
    <hr>
    Начало мероприятия(пример: 1 ноября 2017, 20:00)
    <br>
    <input type="text" name="begin" title="begin" required>
    <hr>
    Конец мероприятия(пример: 1 ноября 2017, 21:00)
    <br>
    <input type="text" name="end" title="end" required>
    <hr>
    <input type="submit" name="submit">
</form>
</body>
</html>



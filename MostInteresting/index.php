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
    pg_query($db, "INSERT INTO public.\"MostInteresting\"(content)
    VALUES('" . $data['content'] ."')");

}

$results = pg_query($db, "SELECT id, content FROM public.\"MostInteresting\";");
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
            <td><? echo $result['content']; ?></td>
            <td><? echo "<a href='delete.php?id=" . $result['id']. "'>Удалить запись</a>"?></td>
            <td><? echo "<a href='edit.php?id=" . $result['id']. "'>Изменить запись</a>"?></td>
        </tr>
    </table>
<? } ?>
<form action="index.php" method="POST">
    Содежание
    <br>
    <input type="text" name="content" title="content" required>
    <br>
    <input type="submit" name="submit">
</form>
</body>
</html>



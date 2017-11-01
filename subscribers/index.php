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

$results = pg_query($db, "SELECT id, email FROM public.\"Subscribers\";");
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
echo "<a href='/feedback/'>Самое интересное</a><br>";
?>

<? foreach ($results as $result) { ?>
    <table style="width: 100%">
        <tr>
            <td><? echo $result['id']; ?></td>
            <td><? echo $result['email']; ?></td>
        </tr>
    </table>
<? } ?>
</body>
</html>



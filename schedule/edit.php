<?php

$db = pg_connect("dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require");

$results = pg_query($db, "SELECT id, title, date_begin, date_end, time_begin, time_end
	FROM public.\"Schedule\" WHERE id=". $_GET['id'] . ";");
$results = pg_fetch_all($results);

if ($_POST['submit']) {
    $data = $_POST;
    pg_query($db, "UPDATE public.\"Schedule\"
	SET title='" . $data['title'] . "', date_begin='". $data['date_begin'] . "', date_end='" . $data['date_end'] . "', time_begin='" . $data['time_begin'] . "', time_end='" . $data['time_end'] ."'
	WHERE id = 20;");

    header('Location: /schedule/', true, 301);
}

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
    <form action="edit.php" method="POST">
        Название мероприятия
        <br>
        <input type="text" name="title" title="title" value="<? echo $result['title'] ?>" required>
        <hr>
        Дата начала мероприятия(пример: 1 ноября 2017)
        <br>
        <input type="text" name="date_begin" title="date_begin" value="<? echo $result['date_begin'] ?>" required>
        <hr>
        Время начала мероприятия(пример: 09:30)
        <br>
        <input type="text" name="time_begin" title="time_begin" value="<? echo $result['time_begin'] ?>" required>
        <hr>
        Дата конца мероприятия(пример: 1 ноября 2017)
        <br>
        <input type="text" name="date_end" title="date_end" value="<? echo $result['date_end'] ?>" required>
        <hr>
        Время конца мероприятия(пример: 09:30)
        <br>
        <input type="text" name="time_end" title="time_end" value="<? echo $result['time_end'] ?>" required>
        <hr>
        <input type="submit" name="submit">
    </form>
<? } ?>
</body>
</html>

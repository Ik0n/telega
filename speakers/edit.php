<?php

$db = pg_connect("dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require");

$results = pg_query($db, "SELECT id, name, about, refphoto, session FROM public.\"Speakers\" WHERE id=". $_GET['id'] . ";");
$results = pg_fetch_all($results);

if ($_POST['submit']) {
    $data = $_POST;
    pg_query($db, "UPDATE public.\"Speakers\"
	SET name='" . $data['name'] ."',about='" . $data['about'] . "',refphoto='" . $data['refphoto'] . "',session='" . $data['session'] . "'
	WHERE id=" . $_GET['id'] .";");

    header('Location: /speakers/', true, 301);
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
<form action="edit.php?id=<? $_GET['id'] ?>" method="POST">
    ФИО спикера
    <br>
    <input type="text" name="name" title="name" value="<?echo $result['name'] ?>" required>
    <hr>
    О спикере
    <br>
    <input type="text" name="about" title="about" value="<?echo $result['about'] ?>" required>
    <hr>
    Ссылка на фотографию спикера
    <br>
    <input type="text" name="refphoto" title="refphoto" value="<?echo $result['refphoto'] ?>" required>
    <hr>
    Сессия в которой участвует спикер
    <br>
    <input type="text" name="session" title="session" value="<?echo $result['session'] ?>" required>
    <hr>
    <input type="submit" name="submit">
</form>
<? } ?>
</body>
</html>

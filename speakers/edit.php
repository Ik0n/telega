<?php

$db = pg_connect("dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require");

$results = pg_query($db, "SELECT id, first_name, last_name, about, refphoto, session FROM public.\"Speakers\" WHERE id=". $_GET['id'] . ";");
$results = pg_fetch_all($results);

if ($_POST['submit']) {
    $data = $_POST;
    pg_query($db, "UPDATE public.\"Speakers\"
	SET first_name='" . $data['first_name'] . "',last_name='" . $data['last_name'] . "',about='" . $data['about'] . "',refphoto='" . $data['refphoto'] . "',session='" . $data['session'] . "'
	WHERE id=" . $data['id'] .";");

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
<form action="edit.php" method="POST">
    <input type="hidden" name="id" title="id" value="<?echo $result['id']?>">
    Фамилия спикера
    <br>
    <input type="text" name="last_name" title="last_name" value="<?echo $result['last_name'] ?>" required>
    <hr>
    Имя спикера
    <br>
    <input type="text" name="first_name" title="first_name" value="<?echo $result['first_name'] ?>" required>
    <hr>
    О спикере
    <br>
    <textarea name="about" id="about" title="about" cols="30" rows="10"><?echo $result['about']?></textarea>
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

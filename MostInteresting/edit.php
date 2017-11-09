<?php

$db = pg_connect("dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require");

$results = pg_query($db, "SELECT id, content FROM public.\"MostInteresting\" WHERE id=" . $_GET['id'] . ";");
$results = pg_fetch_all($results);


if ($_POST['submit']) {
    $data = $_POST;
    pg_query($db, "UPDATE public.\"MostInteresting\"
	SET content='" . $data['content'] . "'
	WHERE id =" . $data['id'] . ";");

    header('Location: index.php', true, 301);
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
            Содежание
            <br>
            <input type="text" name="content" title="content" value="<?echo $result['content']?>" required>
            <br>
        <input type="submit" name="submit">
    </form>
<? } ?>
</body>
</html>

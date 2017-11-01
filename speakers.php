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
        pg_query($db, "INSERT INTO public.\"Speakers\"(
	name, about, refphoto, session)
	VALUES (". $data['name'] ."," . $data['about'] ."," . $data['refphoto'] . "," . $data['session'] . ");");

    }

    $results = pg_query($db, "SELECT id, name, about, refphoto, session FROM public.\"Speakers\" ORDER BY id;");
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script defer src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</head>
<body>
    <? foreach ($results as $result) { ?>
        <table class="table table-bordered">
            <tr>
                <td><? echo $result['id']; ?></td>
                <td><? echo $result['name']; ?></td>
                <td><? echo $result['about']; ?></td>
                <td><? echo $result['refphoto']; ?></td>
                <td><? echo $result['session']; ?></td>
            </tr>
        </table>
    <? } ?>
    <form action="speakers.php" method="POST">
        ФИО спикера
        <br>
        <input type="text" name="name" title="name" required>
        <hr>
        О спикере
        <br>
        <input type="textarea" rows="10" cols="45" name="about" title="about" required>
        <hr>
        Ссылка на фотографию спикера
        <br>
        <input type="text" name="refphoto" title="refphoto" required>
        <hr>
        Сессия в которой участвует спикер
        <br>
        <input type="text" name="session" title="session" required>
        <hr>
        <input type="submit" name="submit">
    </form>
</body>
</html>



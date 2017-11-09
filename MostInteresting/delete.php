<?php
/**
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 01.11.2017
 * Time: 17:59
 */

if (isset($_GET['id'])) {
    $db = pg_connect("dbname=d4re8r18uqsqa 
                host=ec2-46-51-187-253.eu-west-1.compute.amazonaws.com 
                port=5432 
                user=nhtxzmrecgoswb 
                password=078e8a10351abf96961014d551717ef2b4fb31ce260b31ea5ebd24d3aff823b0 
                sslmode=require");
    pg_query($db, "DELETE FROM public.\"MostInteresting\" WHERE id =" . $_GET['id'] . ";");

    header('Location: index.php', true, 301);
}
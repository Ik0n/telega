<?php
/**
 * Created by PhpStorm.
 * User: Ik0n1
 * Date: 02.11.2017
 * Time: 22:17
 */

class TelegramBot
{
    public $counterForSelectDB;

    /**
     * @param mixed $counterForSelectDB
     */
    public function setCounterForSelectDB($counterForSelectDB = 6)
    {
        $this->counterForSelectDB = $counterForSelectDB;
    }

    /**
     * @return mixed
     */
    public function getCounterForSelectDB()
    {
        return $this->counterForSelectDB;
    }
}
<?php


namespace AppBundle\Factory;


class UnknowAuthorException extends \Exception
{

    public function __construct($data)
    {
        $data = json_encode($data);
        $this->message = "Couldn't determine author of message. Data: $data";
    }

}
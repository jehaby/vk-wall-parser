<?php


namespace AppBundle\Factory;


class PostCreationException extends \Exception
{

    public function __construct($data)
    {
        $data = json_encode($data);
        $this->message = "Couldn't create post with such data: $data";
    }

}
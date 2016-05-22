<?php


namespace AppBundle\Service;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;


class MailerService
{

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Twig_Environment
     */
    protected $twig;


    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }


    public function send($posts)
    {
        if (count($posts) === 0) {
            return;
        }

        $messageBody = $this->twig->render('mail.html.twig', ['posts' => $posts]);

        $message = Swift_Message::newInstance()
            ->setSubject('Новые сообщения в уютном гнездышке')
            ->setFrom('vk-parser@stasmakarov.ru')
            ->setTo('jehaby@ya.ru')
            ->setBody(
                $messageBody,
                'text/html'
            );

        $res = $this->mailer->send($message);

        return $res;
    }

}
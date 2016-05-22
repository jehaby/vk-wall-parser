<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\SearchQuery;
use AppBundle\Factory\PostFactory;
use AppBundle\Repository\SearchQueryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\Exception\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $ps = $this->get('app.parser_service');
        $ps->parse();

        return $this->render('index.html.twig');

    }
}

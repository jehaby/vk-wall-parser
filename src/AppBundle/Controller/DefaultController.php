<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\SearchQuery;
use AppBundle\Factory\PostFactory;
use AppBundle\Repository\SearchQueryRepository;
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
        $sq = new SearchQuery('wtf');

        $em = $this->get('doctrine.orm.entity_manager');
//        dump($em->persist($sq));
//
//        $em->flush();

        $r = $em->getRepository(SearchQuery::class);

        dump($r->findAll());

        die();

        $version = '5.52';

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://api.vk.com/']);

        $baseOptions = [
            'v' => '5.52',
            'domain' => 'yuytnoe_gnezdishko'
        ];


        $res = $client->get('method/wall.search', [
            'query' => array_replace_recursive($baseOptions, [
//                'query' => 'петроградская',
                'count' => 100,
                'owners_only',
            ])
        ]);

        $res = \GuzzleHttp\json_decode($res->getBody()->getContents(), true)['response']['items'];

        $postFactory = $this->get('app.post_factory');

        foreach ($res as $item) {
            try {
                $posts[] = $postFactory->createFromArray($item);
            } catch (\Exception $e) {

            }
        }

        dump($posts, $res);
        die();


        $res2 = array_filter($res, function ($item) {
            return (!isset($item['signer_id']));
        });

        dump($res2, $res);
        die();

        $withoutSignerId = 0;


        foreach ($res['response']['items'] as $item) {
            if (!isset($item['signer_id'])) {
                $withoutSignerId++;
            }
        }

        dump($withoutSignerId);


        $res = array_map(function ($item) {
            return new Post(
                $item
            );
        }, $res['response']['items']);


        $res = $client->get('method/wall.search', [
            'query' => array_replace_recursive($baseOptions, [
                'query' => 'петроградская',
                'count' => 100,
                'owners_only' => 1,
            ])
        ]);


        $res = \GuzzleHttp\json_decode($res->getBody()->getContents(), true);


        dump($res);


        die();


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }
}

<?php

namespace AppBundle\Service;


use AppBundle\Repository\PostRepository;
use AppBundle\Repository\SearchQueryRepository;
use AppBundle\Entity\SearchQuery;
use AppBundle\Entity\Post;
use AppBundle\Factory\PostFactory;
use GuzzleHttp\Client;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ParserService
 */
class ParserService
{

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var SearchQueryRepository
     */
    private $searchQueryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Client;
     */
    private $httpClient;


    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * ParserService constructor.
     * @param PostRepository $postRepository
     * @param SearchQueryRepository $searchQueryRepository
     * @param EntityManagerInterface $entityManager
     * @param PostFactory $postFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        PostRepository $postRepository,
        SearchQueryRepository $searchQueryRepository,
        EntityManagerInterface $entityManager,
        PostFactory $postFactory,
        LoggerInterface $logger
    )
    {
        $this->postRepository = $postRepository;
        $this->searchQueryRepository = $searchQueryRepository;
        $this->entityManager = $entityManager;
        $this->postFactory = $postFactory;
        $this->httpClient = new Client(['base_uri' => 'http://api.vk.com/']);
        $this->logger = $logger;
    }

    /**
     *
     */
    public function parse()
    {
        foreach ($this->searchQueryRepository->findAll() as $searchQeury) {
            $this->processQuery($searchQeury);
        }
    }

    protected function processQuery(SearchQuery $query)
    {
        $postsFromApi = $this->getPostsFromVk($query);

        /** @var Post[] $postsInDb */
        $postsInDb = $this->postRepository->findBy(['id' => $postsFromApi->getKeys()]);

        // TODO: try to use other posts (gotten from api)
        foreach ($postsInDb as $post) {
            if (!$post->belongsToSearchQuery($query)) {
                $post->addSearchQuery($query);
            }
            $postsFromApi->remove($post->getId());
            $this->entityManager->persist($post);
        }

        foreach ($postsFromApi as $post) {
            $this->entityManager->persist($post);
        }

        $this->entityManager->flush();

        /*
         * search new post
         * get their ids
         * query db for saved posts with such ids (lazy loading related searchqeury)
         * if post exists, check if it's new query
         */
    }

    protected function getPostsFromVk(SearchQuery $query)
    {
        $baseOptions = [
            'v' => '5.52',
            'domain' => 'yuytnoe_gnezdishko'
        ];

        $res = $this->httpClient
            ->get(
                'method/wall.search',
                [
                    'query' => array_replace_recursive($baseOptions, [
                        'query' => $query->getText(),
                        'count' => 100,
                        'owners_only',
                    ])
                ]
            )
            ->getBody()
            ->getContents();

        $res = \GuzzleHttp\json_decode($res, true)['response']['items'];
        $postsFromApi = new ArrayCollection();
        foreach ($res as $post) {
            try {
                $postId = $post['id'];
                $postsFromApi->set($postId, $this->postFactory->createFromArray($post, $query));
            } catch (\Exception $e) {
                continue;
            }
        }
        return $postsFromApi;
    }


}
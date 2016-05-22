<?php

namespace AppBundle\Service;


use AppBundle\Repository\PostRepository;
use AppBundle\Repository\SearchQueryRepository;
use AppBundle\Entity\SearchQuery;
use AppBundle\Entity\Post;
use AppBundle\Service\MailerService;
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
    protected $postRepository;

    /**
     * @var SearchQueryRepository
     */
    protected $searchQueryRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Client;
     */
    protected $httpClient;

    /**
     * @var MailerService
     */
    protected $mailer;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * ParserService constructor.
     * @param PostRepository $postRepository
     * @param SearchQueryRepository $searchQueryRepository
     * @param EntityManagerInterface $entityManager
     * @param MailerService $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(
        PostRepository $postRepository,
        SearchQueryRepository $searchQueryRepository,
        EntityManagerInterface $entityManager,
        MailerService $mailer,
        LoggerInterface $logger
    )
    {
        $this->postRepository = $postRepository;
        $this->searchQueryRepository = $searchQueryRepository;
        $this->entityManager = $entityManager;
        $this->httpClient = new Client(['base_uri' => 'http://api.vk.com/']);
        $this->mailer = $mailer;
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

        $this->mailer->send($postsFromApi);
        
        foreach ($postsFromApi as $post) {
            $this->entityManager->persist($post);
        }

        $this->entityManager->flush();
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
                        'owners_only' => 1,
                    ])
                ]
            )
            ->getBody()
            ->getContents();

        $res = \GuzzleHttp\json_decode($res, true)['response']['items'];

        $postsFromApi = new ArrayCollection();

        foreach ($res as $post) {
            try {
                $postsFromApi->set($postId = $post['id'], Post::createFromApiArray($post, $query));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        }

        return $postsFromApi;
    }
    
    
    

}
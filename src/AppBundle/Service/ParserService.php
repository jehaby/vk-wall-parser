<?php


use AppBundle\Repository\PostRepository;
use AppBundle\Repository\SearchQueryRepository;
use AppBundle\Entity\SearchQuery;
use AppBundle\Entity\Post;
use GuzzleHttp\Client;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;


    /**
     * ParserService constructor.
     * @param PostRepository $postRepository
     * @param SearchQueryRepository $searchQueryRepository
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        PostRepository $postRepository,
        SearchQueryRepository $searchQueryRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->postRepository = $postRepository;
        $this->searchQueryRepository = $searchQueryRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->httpClient = new Client(['base_uri' => 'http://api.vk.com/']);        
    }

    /**
     *
     */
    public function parse()
    {
        foreach ($this->searchQueryRepository->findAll() as $searchQeury) {
            
        }
    }
    
    protected function searchWall(SearchQuery $query) {
        $res = $this->httpClient->get('method/wall.search', [
            'query' => array_replace_recursive($baseOptions, [
                'query' => $query->getText(),
                'count' => 100,
                'owners_only',
            ])
        ]);

    }

}
<?php

namespace AppBundle\Service;


use AppBundle\Repository\PostRepository;
use AppBundle\Repository\SearchQueryRepository;
use AppBundle\Entity\SearchQuery;
use AppBundle\Entity\Post;
use AppBundle\Factory\PostFactory;
use Doctrine\Common\Collections\Criteria;
use GuzzleHttp\Client;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Cake\Chronos\Chronos;

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
     * @var PostFactory
     */

    protected $postFactory;

    /**
     * @var SamePostsDetector
     */
    protected $detector;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * ParserService constructor.
     * @param PostRepository $postRepository
     * @param SearchQueryRepository $searchQueryRepository
     * @param EntityManagerInterface $entityManager
     * @param PostFactory $postFactory
     * @param SamePostsDetector $detector
     * @param LoggerInterface $logger
     */
    public function __construct(
        PostRepository $postRepository,
        SearchQueryRepository $searchQueryRepository,
        EntityManagerInterface $entityManager,
        PostFactory $postFactory,
        SamePostsDetector $detector,
        LoggerInterface $logger
    )
    {
        $this->postRepository = $postRepository;
        $this->searchQueryRepository = $searchQueryRepository;
        $this->entityManager = $entityManager;
        $this->postFactory = $postFactory;
        $this->httpClient = new Client(['base_uri' => 'http://api.vk.com/']);
        $this->detector = $detector;
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

        /** @var Post[] $savedPostsWithSameId */
        $savedPostsWithSameId = $this->postRepository->findBy(['id' => $postsFromApi->getKeys()]);

        // TODO: try to use other posts (gotten from api)
        foreach ($savedPostsWithSameId as $post) {
            if (!$post->belongsToSearchQuery($query)) {
                $post->addSearchQuery($query);
            }
            $postsFromApi->remove($post->getId());
            $this->entityManager->persist($post);
        }

        /** @var Post[] $savedPostsFromSameAuthors */
        $savedPostsFromSameAuthors = $this->getRecentPostsFromSameAuthors($postsFromApi);

        /** @var array[Post[]] $postsFromApi */
        $postsFromApi = $this->groupPostsByAuthor($postsFromApi);

        foreach ($savedPostsFromSameAuthors as $savedPost) {
            $key = $savedPost->getRealAuthorId();

            /** @var ArrayCollection $postsFromSameAuthor */
            $postsFromSameAuthor = $postsFromApi->get($key);

            foreach ($postsFromSameAuthor as $postFromApi) {
                if ($this->detector->isPostsSame($postFromApi->getText(), $savedPost->getText())) {
                    $postsFromSameAuthor->removeElement($postsFromApi);
                }
            }

            foreach ($postsFromSameAuthor as $post) {
                $this->entityManager->persist($post);
            }
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

    /**
     * @param Post[] $posts
     * @return ArrayCollection
     */
    protected function getRecentPostsFromSameAuthors(ArrayCollection $posts)
    {
        $ids = array_map(
            function (Post $post) {
                return $post->getRealAuthorId();
            },
            $posts->getValues()
        );

        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->gt('date', Chronos::now()->subDays(7)->toUnixString()))
            ->andWhere($criteria->expr()->in('realAuthorId', $ids));

        $res = $this->postRepository->matching($criteria);

        return $res;
    }

    /**
     * @param Post[] $posts
     * @return ArrayCollection
     */
    protected function groupPostsByAuthor(ArrayCollection $posts)
    {
        $res = new ArrayCollection();

        foreach ($posts as $post) {
            $key = $post->getRealAuthorId();
            $val = $res->get($key);
            $res->set(
                $key,
                $val ? $val->add($post) : new ArrayCollection([$post])
            );
        }

        return $res;
    }


}
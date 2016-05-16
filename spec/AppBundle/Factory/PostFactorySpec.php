<?php

namespace spec\AppBundle\Factory;

use AppBundle\Entity\Post;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class PostFactorySpec extends ObjectBehavior
{

    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {

        $this->shouldHaveType('AppBundle\Factory\PostFactory');
    }

    function it_creates_posts_from_valid_array()
    {
        $this
            ->createFromArray([
                'id' => 42,
                'text' => 'Продам говно',
                'date' => 2374892383,
                'signer_id' => 32434
            ])
            -> shouldHaveType(Post::class);
    }

    function it_creates_posts_from_valid_array_2()
    {
        $this
            ->createFromArray([
                'id' => 42,
                'text' => 'Продам говно',
                'date' => 2374892383,
                'copy_history' => ['owner_id' => 32434]
            ])
            ->shouldHaveType(Post::class);
    }



}

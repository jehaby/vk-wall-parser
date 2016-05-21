<?php


namespace AppBundle\Service;


class SamePostsDetector
{

    /**
     * @param $postText1
     * @param $postText2
     */
    public function isPostsSame($postText1, $postText2)
    {
        similar_text($postText1, $postText2, $percent);
        return $percent > 60;
    }

}
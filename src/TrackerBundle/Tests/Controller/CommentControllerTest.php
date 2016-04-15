<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    public function testCommentCreatePage()
    {
        $client = static::createClient();
        $client->restart();
        // TODO: Have to be logged in user who's a member of the project this issue is from. Also this project and issue have to exist.

        $crawler = $client->request('GET', '/issue/2/comment/create/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=comment]')->count());
    }
}
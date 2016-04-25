<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use TrackerBundle\Entity\Issue;

class CommentControllerTest extends WebTestCase
{
    const TEST_COMMENT_BODY = 'Test comment';

    // This user and issue should be created from the fixture
    const TEST_USER_NAME = 'trackerbundleOperator';
    const TEST_USER_PASSWORD = 'trackerbundleOperator';
    const TEST_ISSUE_CODE = 'CTE-01';

    /** @var Client $client */
    private $client;

    public function testCommentCreatePage()
    {
        $this->client = static::createClient();
        $this->client->restart();

        $crawler = $this->client->request('GET', '/project/login/');
        $testLoginManager = $this->client->getContainer()->get('tracker.test_login.manager');
        $crawler = $testLoginManager->doLogin($this, $this->client, self::TEST_USER_NAME, self::TEST_USER_PASSWORD);

        /** @var Issue $issue */
        $issue = $this->client->getContainer()
            ->get('doctrine')
            ->getRepository('TrackerBundle:Issue')
            ->findOneByCode(self::TEST_ISSUE_CODE);

        $crawler = $this->client->request('GET', '/issue/' . $issue->getId() . '/comment/create/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=comment]')->count());

        $code = $this->submitCommentForm($crawler);
    }

    /**
     * @param Crawler $crawler
     * @return string
     */
    private function submitCommentForm(Crawler $crawler)
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=comment]')->count());
        $form = $crawler->filter('form[name=comment]')->form();

        $randomPart = (string) random_int(1000000000, 10000000000 - 1);
        $form['comment[body]'] = self::TEST_COMMENT_BODY .' '. $randomPart;
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();

        $this->assertContains(
            self::TEST_COMMENT_BODY,
            $this->client->getResponse()->getContent()
        );
    }
}
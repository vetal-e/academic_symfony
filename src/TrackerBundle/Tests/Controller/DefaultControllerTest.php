<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    // These should be created from the fixtures
    const TEST_USER_NAME = 'trackerbundleOperator';
    const TEST_USER_PASSWORD = 'trackerbundleOperator';
    const TEST_PROJECT_CODE = 'CTE';
    const TEST_PROJECT_LABEL = 'Conquer the Earth!';
    const TEST_ISSUE_CODE = 'CTE-01';
    const TEST_ISSUE_SUMMARY = 'Get things done';

    /** @var Client $client */
    private $client;

    public function testIndex()
    {
        $this->client = static::createClient();
        $this->client->restart();

        $crawler = $this->client->request('GET', '/');

        // For non logged in user there is a redirect to login page
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $testLoginManager = $this->client->getContainer()->get('tracker.test_login.manager');
        $crawler = $testLoginManager->doLogin($this, $this->client, self::TEST_USER_NAME, self::TEST_USER_PASSWORD);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains(
            self::TEST_USER_NAME,
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'Projects',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'Issues',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'Activities',
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            self::TEST_PROJECT_CODE .' '. self::TEST_PROJECT_LABEL,
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            self::TEST_ISSUE_CODE .' '. self::TEST_ISSUE_SUMMARY,
            $this->client->getResponse()->getContent()
        );

        $this->assertContains(
            'created new issue',
            $this->client->getResponse()->getContent()
        );
    }
}

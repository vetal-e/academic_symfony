<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    // This user and issue should be created from the fixture
    const TEST_USER_NAME = 'trackerbundleOperator';
    const TEST_USER_PASSWORD = 'trackerbundleOperator';

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
    }
}

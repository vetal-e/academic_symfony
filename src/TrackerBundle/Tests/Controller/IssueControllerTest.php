<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use TrackerBundle\Entity\Project;

class IssueControllerTest extends WebTestCase
{
    const TEST_ISSUE_SUMMARY = 'Test issue';
    const TEST_ISSUE_CODE = 'TSTISSUE';
    const TEST_ISSUE_TYPE = 'TYPE_TASK';
    const TEST_ISSUE_PRIORITY = 'PRIORITY_HIGH';
    const TEST_ISSUE_DESCRIPTION = 'Test issue description';

    // This user and project should be created from the fixture
    const TEST_USER_NAME = 'trackerbundleOperator';
    const TEST_USER_PASSWORD = 'trackerbundleOperator';
    const TEST_PROJECT_CODE = 'CTE';

    /** @var Client $client */
    private $client;

    public function testIssueCreate()
    {
        $this->client = static::createClient();
        $this->client->restart();

        $crawler = $this->client->request('GET', '/project/login/');
        $testLoginManager = $this->client->getContainer()->get('tracker.test_login.manager');
        $crawler = $testLoginManager->doLogin($this, $this->client, self::TEST_USER_NAME, self::TEST_USER_PASSWORD);

        /** @var Project $project */
        $project = $this->client->getContainer()
            ->get('doctrine')
            ->getRepository('TrackerBundle:Project')
            ->findOneByCode(self::TEST_PROJECT_CODE);

        $crawler = $this->client->request('GET', '/project/' . $project->getId() . '/issue/create/');

        $code = $this->submitIssueForm($crawler);

        $this->removeTestIssue($code);
    }

    /**
     * @param Crawler $crawler
     * @return string
     */
    private function submitIssueForm(Crawler $crawler)
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=issue]')->count());
        $form = $crawler->filter('form[name=issue]')->form();

        $uniquePart = $this->getUniquePart();
        $form['issue[summary]'] = self::TEST_ISSUE_SUMMARY . $uniquePart;
        $form['issue[code]'] = self::TEST_ISSUE_CODE . $uniquePart;
        $form['issue[type]'] = self::TEST_ISSUE_TYPE;
        $form['issue[priority]'] = self::TEST_ISSUE_PRIORITY;
        $form['issue[description]'] = self::TEST_ISSUE_DESCRIPTION;
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();

        $this->assertContains(
            self::TEST_ISSUE_SUMMARY . $uniquePart,
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_ISSUE_CODE . $uniquePart,
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            constant('TrackerBundle\Entity\Issue::' . self::TEST_ISSUE_TYPE),
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            constant('TrackerBundle\Entity\Issue::' . self::TEST_ISSUE_PRIORITY),
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_ISSUE_DESCRIPTION,
            $this->client->getResponse()->getContent()
        );

        return self::TEST_ISSUE_CODE . $uniquePart;
    }

    /**
     * @return string
     */
    private function getUniquePart()
    {
        $randomPart = (string) random_int(1000000000, 10000000000 - 1);
        $summary = self::TEST_ISSUE_SUMMARY . $randomPart;
        $code = self::TEST_ISSUE_CODE . $randomPart;

        $container = $this->client->getContainer();
        $issueRepository = $container->get('doctrine')->getRepository('TrackerBundle:Issue');

        $issueBySummary = $issueRepository->findOneBySummary($summary);
        $issueByCode = $issueRepository->findOneByCode($code);

        if (!empty($issueBySummary) or !empty($issueByCode)) {
            return $this->getUniquePart();
        } else {
            return $randomPart;
        }
    }

    /**
     * @param string $code
     */
    private function removeTestIssue($code)
    {
        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $issueRepository = $container->get('doctrine')->getRepository('TrackerBundle:Issue');

        $issue = $issueRepository->findOneByCode($code);
        if (!empty($issue)) {
            $entityManager->remove($issue);
            $entityManager->flush();
        }
    }
}

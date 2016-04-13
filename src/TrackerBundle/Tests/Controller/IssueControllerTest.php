<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class IssueControllerTest extends WebTestCase
{
    const TEST_ISSUE_SUMMARY = 'Test issue';
    const TEST_ISSUE_CODE = 'TSTISSUE';
    const TEST_ISSUE_TYPE = 'TYPE_TASK';
    const TEST_ISSUE_PRIORITY = 'PRIORITY_HIGH';
    const TEST_ISSUE_DESCRIPTION = 'Test issue description';

    public function testIssueCreate()
    {
        $client = static::createClient();
        $client->restart();
        $crawler = $client->request('GET', '/issue/create/');

        $code = $this->submitIssueForm($client, $crawler);

        $this->removeTestIssue($client, $code);
    }

    /**
     * @param Client $client
     * @param Crawler $crawler
     * @return string
     */
    private function submitIssueForm(Client $client, Crawler $crawler)
    {
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=issue]')->count());
        $form = $crawler->filter('form[name=issue]')->form();

        $uniquePart = $this->getUniquePart($client);
        $form['issue[summary]'] = self::TEST_ISSUE_SUMMARY . $uniquePart;
        $form['issue[code]'] = self::TEST_ISSUE_CODE . $uniquePart;
        $form['issue[type]'] = self::TEST_ISSUE_TYPE;
        $form['issue[priority]'] = self::TEST_ISSUE_PRIORITY;
        $form['issue[description]'] = self::TEST_ISSUE_DESCRIPTION;
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertContains(
            self::TEST_ISSUE_SUMMARY . $uniquePart,
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_ISSUE_CODE . $uniquePart,
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            constant('TrackerBundle\Entity\Issue::' . self::TEST_ISSUE_TYPE),
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            constant('TrackerBundle\Entity\Issue::' . self::TEST_ISSUE_PRIORITY),
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_ISSUE_DESCRIPTION,
            $client->getResponse()->getContent()
        );

        return self::TEST_ISSUE_CODE . $uniquePart;
    }

    /**
     * @param Client $client
     * @return string
     */
    private function getUniquePart(Client $client)
    {
        $randomPart = (string) random_int(1000000000, 10000000000 - 1);
        $summary = self::TEST_ISSUE_SUMMARY . $randomPart;
        $code = self::TEST_ISSUE_CODE . $randomPart;

        $container = $client->getContainer();
        $issueRepository = $container->get('doctrine')->getRepository('TrackerBundle:Issue');

        $issueBySummary = $issueRepository->findOneBySummary($summary);
        $issueByCode = $issueRepository->findOneByCode($code);

        if (!empty($issueBySummary) or !empty($issueByCode)) {
            return $this->getUniquePart($client);
        } else {
            return $randomPart;
        }
    }

    /**
     * @param Client $client
     * @param string $code
     */
    private function removeTestIssue(Client $client, $code)
    {
        $container = $client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $issueRepository = $container->get('doctrine')->getRepository('TrackerBundle:Issue');

        $issue = $issueRepository->findOneByCode($code);
        if (!empty($issue)) {
            $entityManager->remove($issue);
            $entityManager->flush();
        }
    }
}
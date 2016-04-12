<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use TrackerBundle\Entity\Project;

class ProjectControllerTest extends WebTestCase
{
    const TEST_PROJECT_LABEL = 'Test project';
    const TEST_PROJECT_CODE = 'TSTPRJ';
    const TEST_PROJECT_SUMMARY = 'Test project summary';

    public function testProjectCreate()
    {
        $client = static::createClient();
        $client->restart();
        $crawler = $client->request('GET', '/project/create/');

        $code = $this->submitProjectForm($client, $crawler);

        $this->removeTestProject($client, $code);
    }

    public function testProjectEdit()
    {
        $client = static::createClient();
        $client->restart();

        $uniquePart = $this->getUniquePart($client);

        $container = $client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $newProject = new Project();
        $newProject->setLabel(self::TEST_PROJECT_LABEL . $uniquePart);
        $newProject->setCode(self::TEST_PROJECT_CODE . $uniquePart);
        $newProject->setSummary(self::TEST_PROJECT_SUMMARY);
        $entityManager->persist($newProject);
        $entityManager->flush();

        $crawler = $client->request('GET', '/project/edit/' . $newProject->getId());

        $code = $this->submitProjectForm($client, $crawler);

        $this->removeTestProject($client, $code);
    }

    /**
     * @param Client $client
     * @param Crawler $crawler
     * @return string
     */
    private function submitProjectForm(Client $client, Crawler $crawler)
    {
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=project]')->count());
        $form = $crawler->filter('form[name=project]')->form();

        $uniquePart = $this->getUniquePart($client);
        $form['project[label]'] = self::TEST_PROJECT_LABEL . $uniquePart;
        $form['project[code]'] = self::TEST_PROJECT_CODE . $uniquePart;
        $form['project[summary]'] = self::TEST_PROJECT_SUMMARY;
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertContains(
            self::TEST_PROJECT_LABEL . $uniquePart,
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_PROJECT_CODE . $uniquePart,
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_PROJECT_SUMMARY,
            $client->getResponse()->getContent()
        );

        return self::TEST_PROJECT_CODE . $uniquePart;
    }

    /**
     * @param Client $client
     * @return string
     */
    private function getUniquePart(Client $client)
    {
        $randomPart = (string) random_int(1000000000, 10000000000 - 1);
        $label = self::TEST_PROJECT_LABEL . $randomPart;
        $code = self::TEST_PROJECT_CODE . $randomPart;

        $container = $client->getContainer();
        $projectRepository = $container->get('doctrine')->getRepository('TrackerBundle:Project');

        $projectByLabel = $projectRepository->findOneByLabel($label);
        $projectByCode = $projectRepository->findOneByCode($code);

        if (!empty($projectByLabel) or !empty($projectByCode)) {
            return $this->getUniquePart($client);
        } else {
            return $randomPart;
        }
    }

    /**
     * @param Client $client
     * @param string $code
     */
    private function removeTestProject(Client $client, $code)
    {
        $container = $client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $projectRepository = $container->get('doctrine')->getRepository('TrackerBundle:Project');

        $project = $projectRepository->findOneByCode($code);
        if (!empty($project)) {
            $entityManager->remove($project);
            $entityManager->flush();
        }
    }
}

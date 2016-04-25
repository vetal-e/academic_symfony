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

    // This user should be created from the fixture
    const TEST_USER_NAME = 'trackerbundleManager';
    const TEST_USER_PASSWORD = 'trackerbundleManager';

    private $client;

    public function testProjectCreate()
    {
        $this->client = static::createClient();
        $this->client->restart();
        $crawler = $this->client->request('GET', '/project/login/');

        $testLoginManager = $this->client->getContainer()->get('tracker.test_login.manager');
        $crawler = $testLoginManager->doLogin($this, $this->client, self::TEST_USER_NAME, self::TEST_USER_PASSWORD);

        $crawler = $this->client->request('GET', '/project/create/');

        $code = $this->submitProjectForm($crawler);

        $this->removeTestProject($code);
    }

    public function testProjectEdit()
    {
        $this->client = static::createClient();
        $this->client->restart();
        $crawler = $this->client->request('GET', '/project/login/');

        $testLoginManager = $this->client->getContainer()->get('tracker.test_login.manager');
        $crawler = $testLoginManager->doLogin($this, $this->client, self::TEST_USER_NAME, self::TEST_USER_PASSWORD);

        $uniquePart = $this->getUniquePart();

        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $newProject = new Project();
        $newProject->setLabel(self::TEST_PROJECT_LABEL . $uniquePart);
        $newProject->setCode(self::TEST_PROJECT_CODE . $uniquePart);
        $newProject->setSummary(self::TEST_PROJECT_SUMMARY);
        $entityManager->persist($newProject);
        $entityManager->flush();

        $crawler = $this->client->request('GET', '/project/edit/' . $newProject->getId());

        $code = $this->submitProjectForm($crawler);

        $this->removeTestProject($code);
    }

    /**
     * @param Crawler $crawler
     * @return string
     */
    private function submitProjectForm(Crawler $crawler)
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=project]')->count());
        $form = $crawler->filter('form[name=project]')->form();

        $uniquePart = $this->getUniquePart();
        $form['project[label]'] = self::TEST_PROJECT_LABEL . $uniquePart;
        $form['project[code]'] = self::TEST_PROJECT_CODE . $uniquePart;
        $form['project[summary]'] = self::TEST_PROJECT_SUMMARY;
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();

        $this->assertContains(
            self::TEST_PROJECT_LABEL . $uniquePart,
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_PROJECT_CODE . $uniquePart,
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            self::TEST_PROJECT_SUMMARY,
            $this->client->getResponse()->getContent()
        );

        return self::TEST_PROJECT_CODE . $uniquePart;
    }

    /**
     * @return string
     */
    private function getUniquePart()
    {
        $randomPart = (string) random_int(1000000000, 10000000000 - 1);
        $label = self::TEST_PROJECT_LABEL . $randomPart;
        $code = self::TEST_PROJECT_CODE . $randomPart;

        $container = $this->client->getContainer();
        $projectRepository = $container->get('doctrine')->getRepository('TrackerBundle:Project');

        $projectByLabel = $projectRepository->findOneByLabel($label);
        $projectByCode = $projectRepository->findOneByCode($code);

        if (!empty($projectByLabel) or !empty($projectByCode)) {
            return $this->getUniquePart();
        } else {
            return $randomPart;
        }
    }

    /**
     * @param string $code
     */
    private function removeTestProject($code)
    {
        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $projectRepository = $container->get('doctrine')->getRepository('TrackerBundle:Project');

        $project = $projectRepository->findOneByCode($code);
        if (!empty($project)) {
            $entityManager->remove($project);
            $entityManager->flush();
        }
    }
}

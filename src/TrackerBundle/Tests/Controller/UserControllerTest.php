<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TrackerBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    const TEST_USER_EMAIL = 'test@user.com';
    const TEST_USER_NAME = 'testuser';
    const TEST_USER_PASSWORD = 'password';

    /** @var Client $client */
    private $client;

    public function testUserEdit()
    {
        $this->client = static::createClient();
        $this->client->restart();
        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $container = $this->client->getContainer();

        $userRepository = $container->get('doctrine')->getRepository('TrackerBundle:User');
        $existingUser = $userRepository->findOneBy(['email' => self::TEST_USER_EMAIL]);
        if (empty($existingUser)) {
            $userManager = $container->get('fos_user.user_manager');
            /** @var User $newUser */
            $newUser = $userManager->createUser();
            $newUser->setEmail(self::TEST_USER_EMAIL);
            $newUser->setUsername(self::TEST_USER_NAME);
            $newUser->setPlainPassword(self::TEST_USER_PASSWORD);
            $newUser->setEnabled(true);
            $userManager->updateUser($newUser);

            $existingUser = $userRepository->findOneBy(['email' => self::TEST_USER_EMAIL]);
        }

        $userInfo = [
            'id' => $existingUser->getId(),
            'email' => self::TEST_USER_EMAIL . '_edited',
            'username' => self::TEST_USER_NAME . '_edited',
            'fullName' => 'New full name',
            'roles' => ['ROLE_OPERATOR', 'ROLE_MANAGER', 'ROLE_ADMIN'],
        ];

        $testLoginManager = $this->client->getContainer()->get('tracker.test_login.manager');
        $crawler = $testLoginManager->doLogin($this, $this->client, self::TEST_USER_NAME, self::TEST_USER_PASSWORD);

        $this->client = $this->submitUserEditForm($userInfo);

        $this->assertContains(
            $userInfo['email'],
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            $userInfo['username'],
            $this->client->getResponse()->getContent()
        );
        $this->assertContains(
            $userInfo['fullName'],
            $this->client->getResponse()->getContent()
        );
        // this user has no permissions to change roles
        $this->assertContains(
            'Operator',
            $this->client->getResponse()->getContent()
        );

        // Return the user info back to original state
        $userInfo = [
            'id' => $existingUser->getId(),
            'email' => self::TEST_USER_EMAIL,
            'username' => self::TEST_USER_NAME,
            'fullName' => '',
            'roles' => ['ROLE_OPERATOR'],
        ];
        $this->client = $this->submitUserEditForm($userInfo);
    }

    private function submitUserEditForm($userInfo)
    {
        $crawler = $this->client->request('GET', '/user/edit/' . $userInfo['id']);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('form[name=user]')->count());
        $form = $crawler->filter('form[name=user]')->form();

        $form['user[email]'] = $userInfo['email'];
        $form['user[username]'] = $userInfo['username'];
        $form['user[fullName]'] = $userInfo['fullName'];
        $form['user[roles]']->select($userInfo['roles']);
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();

        return $this->client;
    }
}

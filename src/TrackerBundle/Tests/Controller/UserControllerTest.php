<?php

namespace TrackerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    const TEST_USER_EMAIL = 'test@user.com';
    const TEST_USER_NAME = 'testuser';
    const TEST_USER_PASSWORD = 'password';

    public function testUserEdit()
    {
        $client = static::createClient();
        $client->restart();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $container = $client->getContainer();

        $userRepository = $container->get('doctrine')->getRepository('TrackerBundle:User');
        $existingUser = $userRepository->findOneBy(['email' => self::TEST_USER_EMAIL]);
        if (empty($existingUser)) {
            $userManager = $container->get('fos_user.user_manager');
            $newUser = $userManager->createUser();
            $newUser->setEmail(self::TEST_USER_EMAIL);
            $newUser->setUsername(self::TEST_USER_NAME);
            $newUser->setPlainPassword(self::TEST_USER_PASSWORD);
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
        $client = $this->submitUserEditForm($client, $userInfo);

        $this->assertContains(
            $userInfo['email'],
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            $userInfo['username'],
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            $userInfo['fullName'],
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Operator, Manager, Administrator',
            $client->getResponse()->getContent()
        );

        // Return the user info back to original state
        $userInfo = [
            'id' => $existingUser->getId(),
            'email' => self::TEST_USER_EMAIL,
            'username' => self::TEST_USER_NAME,
            'fullName' => '',
            'roles' => ['ROLE_OPERATOR'],
        ];
        $client = $this->submitUserEditForm($client, $userInfo);
    }

    private function submitUserEditForm($client, $userInfo)
    {
        $crawler = $client->request('GET', '/user/edit/' . $userInfo['id']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('form[name=user]')->count());
        $form = $crawler->filter('form[name=user]')->form();

        $form['user[email]'] = $userInfo['email'];
        $form['user[username]'] = $userInfo['username'];
        $form['user[fullName]'] = $userInfo['fullName'];
        $form['user[roles]']->select($userInfo['roles']);
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        return $client;
    }
}

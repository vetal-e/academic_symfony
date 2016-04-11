<?php

namespace TrackerBundle\Tests\FOSUserBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FOSUserBundleTest extends WebTestCase
{
    const TEST_USER_EMAIL = 'test@user.com';
    const TEST_USER_NAME = 'testuser';
    const TEST_USER_PASSWORD = 'password';

    public function testRegister()
    {
        $client = static::createClient();
        $client->restart();
        $crawler = $client->request('GET', '/register/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name=fos_user_registration_form]')->count());
        $this->assertEquals(1, $crawler->filter('form[name=fos_user_registration_form] input[type=submit]')->count());

        $container = $client->getContainer();
        $userRepository = $container->get('doctrine')->getRepository('TrackerBundle:User');
        $existingUser = $userRepository->findOneBy(['email' => self::TEST_USER_EMAIL]);
        if (!empty($existingUser)) {
            $entityManager = $container->get('doctrine')->getManager();
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        $form = $crawler->filter('form[name=fos_user_registration_form]')->form();
        $form['fos_user_registration_form[email]'] = self::TEST_USER_EMAIL;
        $form['fos_user_registration_form[username]'] = self::TEST_USER_NAME;
        $form['fos_user_registration_form[plainPassword][first]'] = self::TEST_USER_PASSWORD;
        $form['fos_user_registration_form[plainPassword][second]'] = self::TEST_USER_PASSWORD;
        $crawler = $client->submit($form);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/register/confirmed')
        );
        $crawler = $client->followRedirect();

        $this->assertContains(
            'The user has been created successfully',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            'Logged in as testuser',
            $client->getResponse()->getContent()
        );
    }

    public function testLogin()
    {
        $client = static::createClient();
        $client->restart();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"] input[type=submit]')->count());

        $form = $crawler->filter('form[action="/login_check"]')->form();
        $form['_username'] = self::TEST_USER_NAME;
        $form['_password'] = self::TEST_USER_PASSWORD;
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertNotContains(
            'Invalid credentials',
            $client->getResponse()->getContent()
        );
    }
}

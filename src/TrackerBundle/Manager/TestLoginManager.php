<?php

namespace TrackerBundle\Manager;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestLoginManager
{
    public function doLogin(WebTestCase $test, Client $client, $username, $password)
    {
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('_submit')->form(array(
            '_username'  => $username,
            '_password'  => $password,
        ));
        $client->submit($form);

        $test->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        return $crawler;
    }
}
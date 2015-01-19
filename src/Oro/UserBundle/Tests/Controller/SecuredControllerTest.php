<?php

namespace Oro\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecuredControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    }

    public function testLoginCheck()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login_check');
    }

}

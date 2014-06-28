<?php

namespace Gmas\MusicBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PagesControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

    public function testListen()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/listen/{playlist}/{song}');
    }

}

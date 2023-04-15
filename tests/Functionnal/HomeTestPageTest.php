<?php

namespace App\Tests\Functionnal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTestPageTest extends WebTestCase
{
    public function testIfHomePageIsSuccessfull(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $button = $crawler->filter('.btn-primary');

        $this->assertEquals(1, count($button));

        $recipes = $crawler->filter('.card');

        $this->assertEquals(3, count($recipes));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur Symfony Recipe');
    }
}

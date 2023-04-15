<?php

namespace App\Tests\Functionnal\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testIfCrudIsHere(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $manager->getRepository(User::class)->findOneBy(['id' => 1]);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/admin');

        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Demandes de contact');

        $client->click($crawler->filter('.action-new')->link());

        $this->assertResponseIsSuccessful();

        $client->request(Request::METHOD_GET, '/admin');

        $client->click($crawler->filter('.action-edit')->link());

        $this->assertResponseIsSuccessful();
    }
}
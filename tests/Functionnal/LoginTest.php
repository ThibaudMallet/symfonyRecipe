<?php

namespace App\Tests\Functionnal;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessfull(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('app_security_login'));

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.fr",
            "_password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('app_home');
    }

    public function testIfLoginFailedWhenPasswordIsWrong(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('app_security_login'));

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.fr",
            "_password" => "passwordWrong"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('app_security_login');

        $this->assertSelectorTextContains('div.alert-danger', 'Invalid credentials.');
    }
}

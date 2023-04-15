<?php

namespace App\Tests\Functionnal;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testIfSendMessageIsSuccessfull(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        $submitButton = $crawler->selectButton('Envoyer mon message');
        $form = $submitButton->form();

        $form['contact[fullName]'] = "Jean Dupont";
        $form['contact[email]'] = "Jd@symrecipe.fr";
        $form['contact[subject]'] = "Test";
        $form['contact[message]'] = "Test";

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // $this->assertEmailCount(1);

        $client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert.alert-success.mt-4',
            'Votre demande a bien été envoyé !'
        );
    }
}

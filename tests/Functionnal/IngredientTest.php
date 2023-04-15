<?php

namespace App\Tests\Functionnal;

use App\Entity\User;
use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $manager->find(User::class, 1);

        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient_new'));

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'Un ingrédient de test',
            'ingredient[price]' => floatval(33)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a bien été ajouté !');
        $this->assertRouteSame('app_ingredient');
    }

    public function testIfListIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $manager->find(User::class, 1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredient'));

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_ingredient');
    }

    public function testIfUpdateIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $manager->find(User::class, 1);

        $ingredient = $manager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user,
        ]);
        
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('app_ingredient_edit', ['id' => $ingredient->getId()]
        ));

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'Un ingrédient de test à modifier',
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a bien été modifié !');
        $this->assertRouteSame('app_ingredient');
    }

    public function testIfDeleteIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $manager->find(User::class, 1);

        $ingredient = $manager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user,
        ]);
        
        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('app_ingredient_delete', ['id' => $ingredient->getId()]
        ));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a bien été supprimé !');
        $this->assertRouteSame('app_ingredient');
    }
}

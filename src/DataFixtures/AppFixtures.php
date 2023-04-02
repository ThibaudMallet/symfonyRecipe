<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i =1; $i < 50; $i++) {
            $ingredient = new Ingredient();
    
            $ingredient->setName($this->faker->word());
            $ingredient->setPrice($this->faker->randomFloat(2, 1, 100));
    
            $manager->persist($ingredient);       
        }

        $manager->flush();
    }
}
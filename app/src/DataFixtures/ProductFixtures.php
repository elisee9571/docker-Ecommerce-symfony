<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= 10; $i++) {

            $product = new Product;
            $product
                ->setName($faker->sentence(2, true))
                ->setSlug($this->slugger->slug($product->getName())->lower())
                ->setContent($faker->sentence(6, true))
                ->setPrice($faker->randomFloat(2, 10, 150))
                ->setQuantity($faker->numberBetween(0, 10))
                ->setCategory($this->getReference('category-' . rand(1, 6)));

            $this->addReference('product-' . $i, $product);

            $manager->persist($product);
        }

        $manager->flush();
    }
}

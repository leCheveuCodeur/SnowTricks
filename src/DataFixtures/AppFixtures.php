<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = ['Grabs', 'Rotations', 'flips', 'slides'];

        foreach ($categories as $category_name) {
            $category = new Category;
            $category->setName($category_name);

            $manager->persist($category);
        }

        $manager->flush();
    }
}

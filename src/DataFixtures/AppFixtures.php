<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Trick;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $categoryRepository, $slugger;

    public function __construct(CategoryRepository $categoryRepository, SluggerInterface $slugger)
    {
        $this->categoryRepository = $categoryRepository;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Category
        $categories = ['grabs', 'flips & off axis', 'slides'];

        foreach ($categories as $category_name) {
            $category = new Category;
            $category->setName($category_name);

            $manager->persist($category);
        }
        $manager->flush();

        // Trick
        $tricks = [
            [
                "Frontside 720", 1, "une rotation de 2 tours en frontside",
                ["colin-lloyd-CVB44XCYyHA-unsplash.jpg", "colin-lloyd-pzZmPqPdAIE-unsplash.jpg"],
                ["https://youtu.be/1vtZXU15e38", "https://youtu.be/H2MKP1epC7k"]
            ],
            [
                "Frontside 540", 1, "saut où l’on fait un tour et demi en l’air",
                ["damiano-lingauri-OKBP1D8Wr4c-unsplash.jpg"],
                ["https://youtu.be/FMHiSF0rHF8"]
            ],
            [
                "Nose Grab", 0, "consiste à saisir la pointe avant de la planche lors d'un saut",
                ["pexels-pixabay-209817.jpg"],
                ["https://youtu.be/M-W7Pmo-YMY"]
            ],
            [
                "Tail Slide", 2, "consiste à glisser sur une barre de slide sur l'arrière de la planche",
                ["pexels-tyler-tornberg-1630716.jpg"],
                ["https://youtu.be/HRNXjMBakwM"]
            ],
            [
                "Slide", 2, "consiste à glisser sur une barre de slide avec la planche dans l'axe de la barre",
                ["jeffrey-brandjes-GCaV0QdCexQ-unsplash.jpg", "pexels-ryan-leeper-4580974.jpg"],
                ["https://youtu.be/NeY6sSsbbZw"]
            ],
            [
                "Japan Air", 0, "grab de snowboard avancé qui ne manquera pas de faire tourner quelques têtes sur la montagne",
                ["lucas-ludwig-WLjpktG2ijY-unsplash.jpg"],
                ["https://youtu.be/X_WhGuIY9Ak"]
            ],
            [
                "Double McTwist 1260", 1, "glissade avec planche perpendiculaire à la barre de slide avec la barre du coté avant de la planche",
                ["jorg-angeli-cCzeLwUCmnM-unsplash.jpg"],
                ["https://youtu.be/qIr2ki4nWkU"]
            ],
            [
                "mute", 2, "saisie de la carre frontside de la planche entre les deux pieds avec la main avant",
                [],
                []
            ],
            [
                "sad", 2, "saisie de la carre backside de la planche, entre les deux pieds, avec la main avant",
                [],
                []
            ],
            [
                "1080", 1, "",
                [],
                []
            ]
        ];

        foreach ($tricks as $trick_data) {
            $trick_category = \array_search($trick_data[1], \array_flip($categories));

            $category = $this->categoryRepository->findOneBy(['name' => $trick_category]);

            $content = !empty($trick_data[2]) ? $trick_data[2] :  $faker->paragraph();

            $trick = new Trick;
            $trick->setTitle($trick_data[0])
                ->setCategory($category)
                ->setContent($content);

            $manager->persist($trick);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\File;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Contribution;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use App\Repository\ContributionRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $userRepository, $categoryRepository, $trickRepository, $contributionRepository, $slugger, $encoder;

    public function __construct(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        ContributionRepository $contributionRepository,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $encoder
    ) {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->trickRepository = $trickRepository;
        $this->contributionRepository = $contributionRepository;
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = ['grabs', 'flips & off axis', 'slides'];

        $tricks = [
            [
                "admin@gmail.com", "Frontside 720", $categories[1], "une rotation de 2 tours en frontside", "",
                ["colin-lloyd-CVB44XCYyHA-unsplash.jpg", "colin-lloyd-pzZmPqPdAIE-unsplash.jpg"],
                ["https://youtu.be/1vtZXU15e38", "https://youtu.be/H2MKP1epC7k"]
            ],
            [
                "admin@gmail.com", "Frontside 540",  $categories[1], "saut où l’on fait un tour et demi en l’air", "",
                ["damiano-lingauri-OKBP1D8Wr4c-unsplash.jpg"],
                ["https://youtu.be/FMHiSF0rHF8"]
            ],
            [
                "admin@gmail.com", "Nose Grab",  $categories[0], "consiste à saisir la pointe avant de la planche lors d'un saut", "",
                ["pexels-pixabay-209817.jpg"],
                ["https://youtu.be/M-W7Pmo-YMY"]
            ],
            [
                "user0@gmail.com", "Tail Slide",  $categories[2], "consiste à glisser sur une barre de slide sur l'arrière de la planche", "",
                ["pexels-tyler-tornberg-1630716.jpg"],
                ["https://youtu.be/HRNXjMBakwM"]
            ],
            [
                "user0@gmail.com", "Slide",  $categories[2], "consiste à glisser sur une barre de slide avec la planche dans l'axe de la barre", "",
                ["jeffrey-brandjes-GCaV0QdCexQ-unsplash.jpg", "pexels-ryan-leeper-4580974.jpg"],
                ["https://youtu.be/NeY6sSsbbZw"]
            ],
            [
                "user0@gmail.com", "Japan Air",  $categories[0], "grab de snowboard avancé qui ne manquera pas de faire tourner quelques têtes sur la montagne", "",
                ["lucas-ludwig-WLjpktG2ijY-unsplash.jpg"],
                ["https://youtu.be/X_WhGuIY9Ak"]
            ],
            [
                "user0@gmail.com", "Double McTwist 1260",  $categories[1], "glissade avec planche perpendiculaire à la barre de slide avec la barre du coté avant de la planche", "",
                ["jorg-angeli-cCzeLwUCmnM-unsplash.jpg"],
                ["https://youtu.be/qIr2ki4nWkU"]
            ],
            [
                "user0@gmail.com", "mute",  $categories[2], "saisie de la carre frontside de la planche entre les deux pieds avec la main avant", "",
                [],
                []
            ],
            [
                "user0@gmail.com", "sad",  $categories[2], "saisie de la carre backside de la planche, entre les deux pieds, avec la main avant", "",
                [],
                []
            ],
            [
                "user0@gmail.com", "1080",  $categories[1], "", "",
                [],
                []
            ]
        ];

        // Admin User
        $admin = new User;
        $hash = $this->encoder->hashPassword($admin, 'password');

        $admin->setPseudo('Admin')
            ->setEmail('admin@gmail.com')
            ->setPassword($hash)
            ->setRoles([User::ROLE_ADMIN]);
        $manager->persist($admin);

        // 5 Users
        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->encoder->hashPassword($user, 'password');

            $user->setPseudo($faker->userName())
                ->setEmail("user$u@gmail.com")
                ->setPassword($hash)
                ->setRoles([User::ROLE_USER, User::ROLE_CONTRIBUTOR]);

            $manager->persist($user);
        }

        // Categories
        foreach ($categories as $category_name) {
            $category = new Category;
            $category->setName($category_name);

            $manager->persist($category);
        }

        $manager->flush();

        // Tricks
        foreach ($tricks as $trick_data) {
            $trick_category = \array_search($trick_data[2], \array_flip($categories));

            $category = $this->categoryRepository->findOneBy(['name' => $trick_category]);

            $lead_in = !empty($trick_data[3]) ? $trick_data[3] :  $faker->sentence();
            $content = !empty($trick_data[4]) ? $trick_data[4] :  $faker->paragraph();

            $trick = new Trick;
            $trick->setTitle($trick_data[1])
                ->setCategory($category)
                ->setLeadIn($lead_in)
                ->setContent($content)
                ->addUser($this->userRepository->findOneBy(['email' => $trick_data[0]]));

            $manager->persist($trick);
        }

        $manager->flush();

        // 1 Contribution
        $contribution = new Contribution;
        $contribution->setUser($this->userRepository->findOneBy(['email' => 'user1@gmail.com']))
            ->setTitle("840 sans les pieds")
            ->setCategory($this->categoryRepository->findOneBy(['name' => $categories[1]]))
            ->setLeadIn($faker->sentence())
            ->setContent($faker->paragraph())
            ->setDate($faker->dateTimeBetween('-6 months'));

        $manager->persist($contribution);

        $manager->flush();

        // IMG Files
        foreach ($tricks as $trick_data) {
            foreach ($trick_data[5] as $trick_file) {
                $file = new File;
                $file->setName(\preg_replace("/\.\w+$/", '', $trick_file))
                    ->setPath($trick_file)
                    ->setType(File::TYPE_IMG)
                    ->setTrick($this->trickRepository->findOneBy(['title' => $trick_data[1]]));

                $manager->persist($file);
            }
        }

        // VIDEO Files
        foreach ($tricks as $trick_data) {
            foreach ($trick_data[6] as $trick_file) {
                $file = new File;
                $file->setName($faker->sentence(\mt_rand(1, 5)))
                    ->setPath($trick_file)
                    ->setType(File::TYPE_VIDEO)
                    ->setTrick($this->trickRepository->findOneBy(['title' => $trick_data[1]]));

                $manager->persist($file);
            }
        }

        // 30 Comments
        $tricksAll = $this->trickRepository->findAll();
        $usersAll = $this->userRepository->findAll();

        for ($c = 0; $c < 30; $c++) {
            $comment = new Comment;
            $comment->setUser($usersAll[\mt_rand(0, 5)]);
            $comment->setTrick($tricksAll[\mt_rand(0, 9)])
                ->setContent($faker->paragraph())
                ->setDate($faker->dateTimeBetween('-6 months'));

            $manager->persist($comment);
        }
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Contribution;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\ContributionRepository;
use App\Service\EmbedlyUrlHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $userRepository, $categoryRepository, $trickRepository, $contributionRepository, $slugger, $encoder, $asciiSlugger, $embedlyUrlHelper;

    public function __construct(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        ContributionRepository $contributionRepository,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $encoder,
        EmbedlyUrlHelper $embedlyUrlHelper
    ) {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->trickRepository = $trickRepository;
        $this->contributionRepository = $contributionRepository;
        $this->slugger = $slugger;
        $this->encoder = $encoder;
        $this->embedlyUrlHelper = $embedlyUrlHelper;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = ['grabs', 'flips & off axis', 'slides'];

        $tricks = [
            [
                $_ENV['ADMIN_EMAIL'], "Frontside 720", $categories[1], "une rotation de 2 tours en frontside", "",
                ["colin-lloyd-CVB44XCYyHA-unsplash-6106326bf0da6.jpg", "colin-lloyd-pzZmPqPdAIE-unsplash-6106326bf271c.jpg"],
                ["https://www.youtube.com/embed/1vtZXU15e38", "https://youtu.be/H2MKP1epC7k"]
            ],
            [
                $_ENV['ADMIN_EMAIL'], "Frontside 540",  $categories[1], "saut o?? l???on fait un tour et demi en l???air", "",
                ["damiano-lingauri-OKBP1D8Wr4c-unsplash-61063372b108a.jpg"],
                ["https://youtu.be/FMHiSF0rHF8"]
            ],
            [
                $_ENV['ADMIN_EMAIL'], "Nose Grab",  $categories[0], "consiste ?? saisir la pointe avant de la planche lors d'un saut", "",
                ["pexels-pixabay-209817-610634b493deb.jpg"],
                ["https://youtu.be/M-W7Pmo-YMY"]
            ],
            [
                "user0@gmail.com", "Tail Slide",  $categories[2], "consiste ?? glisser sur une barre de slide sur l'arri??re de la planche", "",
                ["pexels-tyler-tornberg-1630716-6106353392224.jpg"],
                ["https://youtu.be/HRNXjMBakwM"]
            ],
            [
                "user0@gmail.com", "Slide",  $categories[2], "consiste ?? glisser sur une barre de slide avec la planche dans l'axe de la barre", "",
                ["jeffrey-brandjes-GCaV0QdCexQ-unsplash-610634e72a9c9.jpg", "pexels-ryan-leeper-4580974-610634e72ca25.jpg"],
                ["https://youtu.be/NeY6sSsbbZw"]
            ],
            [
                "user0@gmail.com", "Japan Air",  $categories[0], "grab de snowboard avanc?? qui ne manquera pas de faire tourner quelques t??tes sur la montagne", "",
                ["lucas-ludwig-WLjpktG2ijY-unsplash-61063469723b1.jpg"],
                ["https://youtu.be/X_WhGuIY9Ak"]
            ],
            [
                "user0@gmail.com", "Double McTwist 1260",  $categories[1], "glissade avec planche perpendiculaire ?? la barre de slide avec la barre du cot?? avant de la planche", "",
                ["jorg-angeli-cCzeLwUCmnM-unsplash-61063444998dc.jpg"],
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
        $hash = $this->encoder->hashPassword($admin, $_ENV['ADMIN_PASSWORD']);

        $admin->setPseudo($_ENV['ADMIN_PSEUDO'])
            ->setEmail($_ENV['ADMIN_EMAIL'])
            ->setPassword($hash)
            ->setIsVerified(\true)
            ->setRoles([User::ROLE_ADMIN]);
        $manager->persist($admin);

        // 5 Users
        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->encoder->hashPassword($user, 'password');

            $user->setPseudo($faker->userName())
                ->setEmail("user$u@gmail.com")
                ->setPassword($hash)
                ->setIsVerified(\true)
                ->setRoles([User::ROLE_AUTHOR]);

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
            $trick_category = \array_search($trick_data[2], $categories);

            $category = $this->categoryRepository->findOneBy(['name' => $categories[$trick_category]]);

            $lead_in = !empty($trick_data[3]) ? $trick_data[3] :  $faker->sentence();
            $content = !empty($trick_data[4]) ? $trick_data[4] :  $faker->paragraph();

            $trick = new Trick;
            $trick->setTitle($trick_data[1])
                ->setCategory($category)
                ->setLeadIn($lead_in)
                ->setContent($content)
                ->setCreationDate($faker->dateTime('-6 months'))
                ->setAuthor($this->userRepository->findOneBy(['email' => $trick_data[0]]));

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
            foreach ($trick_data[5] as $key => $trick_img) {
                $img = new Image;
                $img->setFileName($trick_img)
                    ->setOriginalFileName($trick_img)
                    ->setTitle($this->slugger->slug(\preg_replace("/\.\w+$/", '', $trick_img), ' ', 'fr_Fr'))
                    ->setTrick($this->trickRepository->findOneBy(['title' => $trick_data[1]]));
                $key === 0 ? $img->setInFront(1) : null;
                $manager->persist($img);
            }
        }

        // Video
        foreach ($tricks as $trick_data) {
            foreach ($trick_data[6] as $trick_video) {
                $video = new Video;
                $video->setTitle($faker->sentence(\mt_rand(1, 5)))
                    ->setLink($this->embedlyUrlHelper->embed($trick_video))
                    ->setTrick($this->trickRepository->findOneBy(['title' => $trick_data[1]]));

                $manager->persist($video);
            }
        }

        // 30 Comments
        $tricksAll = $this->trickRepository->findAll();
        $usersAll = $this->userRepository->findAll();

        for ($c = 0; $c < 80; $c++) {
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

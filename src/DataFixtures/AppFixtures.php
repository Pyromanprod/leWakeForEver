<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');
        //création compte admin

        $admin = new User();

        $admin->setEmail("a@a.a")
            ->setCreationDate($faker->dateTimeBetween('-2 year','now'))
            ->setPseudo('Daniel Grantts')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword(
                $this->encoder->hashPassword($admin,'Azerty-1')
            )
        ;
        $manager->persist($admin);
        // CREATION DE 50 user aleatior
        for ($i = 0; $i<50; $i++){
            $user = new User();
            $user
                ->setCreationDate($faker->dateTimeBetween('-5 year', 'now'))
                ->setPseudo($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->hashPassword($admin,'a'));
            $manager->persist($user);
        }
        // Création aléatoir de 500 articles
        for ($i=0; $i<500; $i++){
            $article = new Article();
            $article
                ->setTitle($faker->sentence(10))
                ->setContent($faker->paragraph(100))
                ->setPublicationDate($faker->dateTimeBetween('-3 year', 'now'))
                ->setUser($admin);
            $manager->persist($article);
        }
        $manager->flush();


    }
}

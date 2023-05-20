<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(private UserRepository $repo) {}

    public function load(ObjectManager $manager): void
    {
        // Génération d'une DataFixtures de fausses données d'utilisateurs via FakerPHP
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 50; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',10)),0,25));       

            $manager->persist($user);        
        }

        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setPassword('967520ae23e8ee14888bae72809031b98398ae4a636773e18fff917d77679334');       

        $manager->persist($user);
        
        $manager->flush();   
    }
}

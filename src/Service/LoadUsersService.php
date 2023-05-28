<?php
namespace App\Service;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class LoadUsersService
{
    public function loadUsers(ObjectManager $manager): void
    {
        // Génération de fausses données d'utilisateurs via FakerPHP
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 50; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',10)),0,25));       

            $manager->persist($user);        
        }

        // Notre fantastique utilisateur de test:
        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setPassword('967520ae23e8ee14888bae72809031b98398ae4a636773e18fff917d77679334');       

        $manager->persist($user);
        
        $manager->flush();   
    }
}
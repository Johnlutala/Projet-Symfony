<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Recettes;
use App\Entity\Ingredients;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory; // ✅ Ajout de Faker\Factory
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        // ✅ Création de Faker (en français)
        $faker = Factory::create('fr_FR');


        // Users
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setFullName($faker->name());
            $user->setPseudo($faker->optional()->userName());
            $user->setEmail($faker->unique()->email());
            $user->setRoles(['ROLES_USER']);
            $user->setPlainPassword('password');
            $users[] = $user;

            $manager->persist($user);
        }
        // --- Générer les ingrédients ---
        $ingredients = [];
        for ($i = 1; $i <= 50; $i++) {
            $ingredient = new Ingredients();
            $ingredient->setName($faker->word()); // mot aléatoire
            $ingredient->setPrice($faker->numberBetween(100, 1000));
            $ingredient->setUser($users[mt_rand(0, count($users) - 1)]);

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // --- Générer les recettes ---
        for ($i = 1; $i <= 50; $i++) {
            $recette = new Recettes();
            $recette->setName($faker->sentence(3)); // ex: "Tarte aux pommes"
            $recette->setDescription($faker->paragraph());
            $recette->setTime($faker->numberBetween(10, 1440));
            $recette->setPrice($faker->randomFloat(2, 50, 1000));
            $recette->setDifficulty($faker->numberBetween(1, 5));
            $recette->setIsFavorite($faker->boolean());
            $recette->setIsPublic($faker->boolean());
            $recette->setNbrePersonne($faker->numberBetween(1, 50));
            $recette->setUser($users[mt_rand(0, count($users) - 1)]);

            // Ajouter des ingrédients aléatoires
            for ($k = 0; $k < $faker->numberBetween(1, 10); $k++) {
                $recette->addIngredient($faker->randomElement($ingredients));
            }

            $manager->persist($recette);
        }



        $manager->flush();
    }
}

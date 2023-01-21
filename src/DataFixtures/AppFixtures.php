<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private SluggerInterface $slugger;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $user = new User();
        $plainPassword = 'adminadmin';
        $user
            ->setFirstname('Admin')
            ->setLastname('Admin')
            ->setEmail('contact@admin.com')
            ->setPassword($this->passwordHasher->hashPassword($user, $plainPassword))
            ->setPhone($faker->phoneNumber())
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);

        // 5 Users[]
        for ($i = 1; $i <= 5; $i++)
        {
            $address = new Address();
            $address
                ->setStreet($faker->streetAddress())
                ->setPostal($faker->postcode())
                ->setCity($faker->city())
                ->setCountry($faker->country());
            $manager->persist($address);

            $user = new User();
            $plainPassword = 'password';
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail(strtolower($user->getFirstname()).'@gmail.com')
                ->setPassword($this->passwordHasher->hashPassword($user, $plainPassword))
                ->setPhone($faker->phoneNumber())
                ->setRoles(["ROLE_USER"])
                ->setAddress($address);
            $manager->persist($user);
        }

        // 100 Produits[]
        for ($i = 1; $i <= 100; $i++) {
            $product = new Product();
            $product
                ->setTitle($faker->words(3, true))
                ->setSlug(strtolower($this->slugger->slug($product->getTitle())))
                ->setPrice(mt_rand(5, 300))
                ->setDetail($faker->paragraphs(mt_rand(1, 4), true))
                ->setStock(mt_rand(0, 200))
                ->setCreatedAt($faker->dateTime())
                ->setUpdatedAt($faker->dateTime())
                ->setPublishedAt($faker->dateTime())
                ->setIsPublished(true);
            $manager->persist($product);
        }

        $manager->flush();
    }
}

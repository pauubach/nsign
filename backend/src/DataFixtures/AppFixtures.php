<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoryEnum = [
            "Alimentación",
            "Electrónica",
            "Hogar",
            "Ropa",
            "Salud y belleza"
        ];

        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setName($categoryEnum[$i]);
            $manager->persist($category);
        }
        $manager->flush();
        $allCategories = $manager->getRepository(Category::class)->findAll();
        for ($i = 0; $i < 100; $i++)
        {
            $product=new Product();
            $product->setTitle("Producto $i")
            ->setPrice(mt_rand(10, 100000)/100)
            ->setCategory($allCategories[mt_rand(0,4)])
            ->setstatus(mt_rand(0,1)?'ACTIVO':'INACTIVO')
            ->setImage(file_exists("assets/images/$i.jpg")?"images/$i.jpg":NULL);
            $manager->persist($product);
        }

        $manager->flush();

    }
}
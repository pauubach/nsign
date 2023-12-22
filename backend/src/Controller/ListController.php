<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ListController extends ApiController
{
    #[Route('/list', name: 'app_list')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $products = $doctrine->getRepository(Product::class)->findAll();
        $productList = [];
        foreach ($products as $product) {
            $productList[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'status' => $product->getStatus(),
                'price' => $product->getPrice(),
                'image' => $product->getImage(),
                'category'=> $product->getCategory()->getName()
            ];
        }
        return $this->json($productList);
    }

    #[Route('/del', name: 'api_del_products')]
    public function hola(ManagerRegistry $doctrine): JsonResponse
    {
        dd("HOLA");
    }
}
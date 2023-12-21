<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

#[Route('/api/list')]
class ListController extends AbstractController
{
    #[Route('/', name: 'app_list')]
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
                'category'=> $product->getCategory()->getName()
            ];
        }
        return $this->json($productList);
    }
}
<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/category')]
class CategoryController extends ApiController
{
#[Route('/', name: 'api_categories')]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $categoryList = [];
        foreach ($categories as $category) {
            $categoryList[] = [
                'name' => $category->getName(),
            ];
        }
        return $this->json($categoryList);
    }

    #[Route('/create', name: 'api_categories_create')]
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $name = $request->get('category');

        $category = new Category($name);
        $category->setName($name);

        $this->em->persist($category);
        $this->em->flush();
        return $this->respondWithSuccess(sprintf('Category %s creada', $name));
    }
}
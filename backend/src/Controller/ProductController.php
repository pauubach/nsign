<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/product')]
class ProductController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'api_product_list')]
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

    #[Route('/delete', name: 'api_product_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $ids = $request->get('id');

        foreach ($ids as $id) {
            $product = $doctrine->getRepository(Product::class)->find($id);
            $this->em->remove($product);
        }
        $this->em->flush();
        return $this->respondWithSuccess('Producto/s eliminado/s');
    }

    #[Route('/create', name: 'api_product_create')]
    public function create(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): JsonResponse
    {
        $request_json = $this->transformJsonBody($request);
        $id = $request_json->get('id');
        $title = $request_json->get('title');
        $category_name = $request_json->get('category');
        $status = $request_json->get('status');
        $price = $request_json->get('price');

        // if($request_json->get('image'))
        $imageFile = $request->files->get('image');

        $file = '';
        if ($imageFile) {
            $file = 'images/'.$fileUploader->upload($imageFile);
        }

        $category = $doctrine->getRepository(Category::class)->findOneByName($category_name);
        if (!$category) {
            $category = new Category($category_name);
            $category->setName($category_name);
            $this->em->persist($category);
            $this->em->flush();
        }

        if ($id) {
            $product = $doctrine->getRepository(Product::class)->find($id);
        }
        if (!$product) {
            $product = new Product();
        }

        $product->setTitle($title)
            ->setStatus($status)
            ->setPrice($price)
            ->setCategory($category)
            ->setImage($file);

        $this->em->persist($product);
        $this->em->flush();

        return $this->respondWithSuccess($request_json->get('image'));
    }
}
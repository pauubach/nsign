<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageController extends ApiController
{
    #[Route('api/image', name: 'app_image', methods:["POST"])]
    public function getImage(Request $request): BinaryFileResponse
    {
        $request = $this->transformJsonBody($request);
        $file = $request->get('file');
        $path = "../assets/$file";
        $response = new BinaryFileResponse($path);

        return $response;
    }
}
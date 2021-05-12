<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(ProductCategory::class);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'categories' => $categoryRepository->findAll()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product-category")
 */
class ProductCategoryController extends AbstractController
{
    /**
     * @Route("/{id}", name="product_category_show", methods={"GET"})
     */
    public function show(ProductCategory $productCategory): Response
    {
        return $this->render('product_category/show.html.twig', [
            'category' => $productCategory,
        ]);
    }
}

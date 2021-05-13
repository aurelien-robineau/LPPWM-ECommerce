<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/basket")
 */
class BasketController extends AbstractController
{
    /**
     * @Route("/", name="basket")
     */
    public function index(): Response
    {
        $user = $this->getUser();
    
        $productsPrice = 0.00;
        $numberOfProducts = 0;
        $canMakeOrder = true;
        foreach ($user->getBasket() as $item) {
            $productsPrice += $item->getProduct()->getPrice() * $item->getQuantity();
            $numberOfProducts += $item->getQuantity();

            if ($item->getProduct()->getQuantity() === 0 || $item->getProduct()->getQuantity() < $item->getQuantity()) {
                $canMakeOrder = false;
            }
        }

        $shippingPrice = ProductController::getShippingPrice($productsPrice);

        $totalPrice = $productsPrice + $shippingPrice;

        return $this->render('basket/index.html.twig', [
            'shippingPrice' => $shippingPrice > 0 ? number_format($shippingPrice, 2) . '€' : 'Offerts',
            'totalPrice' => number_format($totalPrice, 2) . '€',
            'numberOfProducts' => $numberOfProducts,
            'canMakeOrder' => $canMakeOrder
        ]);
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        // Always add categories for navbar on render
        $categoryRepository = $this->getDoctrine()->getRepository(ProductCategory::class);
        $parameters['categories'] = $categoryRepository->findAll();

        return parent::render($view, $parameters, $response);
    }
}

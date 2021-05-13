<?php

namespace App\Controller;

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
        foreach ($user->getBasket() as $item) {
            $productsPrice += $item->getProduct()->getPrice();
            $numberOfProducts++;
        }

        $shippingPrice = ProductController::getShippingPrice($productsPrice);

        $totalPrice = $productsPrice + $shippingPrice;

        return $this->render('basket/index.html.twig', [
            'shippingPrice' => $shippingPrice > 0 ? number_format($shippingPrice, 2) . '€' : 'Offerts',
            'totalPrice' => number_format($totalPrice, 2) . '€',
            'numberOfProducts' => $numberOfProducts
        ]);
    }
}

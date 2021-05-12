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
        foreach ($user->getBasket() as $item) {
            $productsPrice += $item->getProduct()->getPrice();
        }

        $shippingPrice = self::getShippingPrice($productsPrice);

        $totalPrice = $productsPrice + $shippingPrice;

        return $this->render('basket/index.html.twig', [
            'shippingPrice' => $shippingPrice > 0 ? number_format($shippingPrice, 2) . ' €' : 'Offerts',
            'totalPrice' => number_format($totalPrice, 2) . ' €'
        ]);
    }

    private static function getShippingPrice(int $orderPrice): float
    {
        if ($orderPrice >= 100)
            return 0.0;
        else if ($orderPrice >= 50)
            return 4.99;
        else if ($orderPrice >= 30)
            return 9.99;
        else if ($orderPrice >= 20)
            return 14.99;
        else
            return 19.99;
    }
}

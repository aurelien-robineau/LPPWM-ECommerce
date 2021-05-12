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
    
        $totalPrice = 0.00;
        foreach ($user->getBasket() as $item) {
            $totalPrice += $item->getProduct()->getPrice();
        }

        return $this->render('basket/index.html.twig', [
            'totalPrice' => number_format($totalPrice, 2),
        ]);
    }
}

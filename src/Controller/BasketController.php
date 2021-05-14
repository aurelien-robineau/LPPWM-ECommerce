<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Entity\UserBasket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'shippingPrice' => $shippingPrice,
            'totalPrice' => $totalPrice,
            'numberOfProducts' => $numberOfProducts,
            'canMakeOrder' => $canMakeOrder
        ]);
    }

    /**
     * @Route("/edit-item/{id}", name="basket_edit", methods={"POST"})
     */
    public function editItem(Request $request, UserBasket $basketItem): Response
    {
        $user = $this->getUser();

        if ($basketItem->getUser()->getId() ===$user->getId()) {
            $em = $this->getDoctrine()->getManager();

            $basketItem->setQuantity((int) $request->request->get('quantity'));
            $em->flush();
        }

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/remove-item/{id}", name="basket_remove_item", methods={"POST"})
     */
    public function removeItem(UserBasket $basketItem): Response
    {
        $user = $this->getUser();

        if ($basketItem->getUser()->getId() === $user->getId()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($basketItem);
            $em->flush();
        }

        return $this->redirectToRoute('basket');
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        // Always add categories for navbar on render
        $categoryRepository = $this->getDoctrine()->getRepository(ProductCategory::class);
        $parameters['categories'] = $categoryRepository->findAll();

        return parent::render($view, $parameters, $response);
    }
}

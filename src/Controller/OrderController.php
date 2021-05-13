<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="orders")
     */
    public function index(): Response
    {
        $orders = $this->getUser()->getOrders();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/{id}", name="order_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/new", name="order_new", methods={"POST"})
     */
    public function new(): Response
    {
        $user = $this->getUser();

        if (count($user->getBasket()) === 0)
            return $this->redirectToRoute('basket');

        $em = $this->getDoctrine()->getManager();
        $order = new Order();

        $productsPrice = 0.00;
        foreach ($user->getBasket() as $item) {
            $product = $item->getProduct();

            if ($product->getQuantity() > 0) {
                $orderProduct = new OrderProduct();
                $orderProduct->setProduct($product);
                $orderProduct->setUnitPrice($product->getPrice());

                $order->addOrderProduct($orderProduct);

                $productsPrice += $product->getPrice();
            }

            $em->remove($item);
        }

        $order->setReference(strtoupper(uniqid()));
        $order->setShippingPrice(ProductController::getShippingPrice($productsPrice));
        $order->setDate(new DateTime());

        $user->addOrder($order);

        $em->flush();

        return $this->redirectToRoute('order_show', ['id' => $order->getId()]);
    }
}

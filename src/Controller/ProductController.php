<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
	/**
	 * @Route("/{id}", name="product_show", methods={"GET"})
	 */
	public function show(Product $product): Response
	{
		return $this->render('product/show.html.twig', [
			'product' => $product,
		]);
	}

	/**
	 * @Route("/{id}/add-to-basket", name="add_product_to_basket", methods={"POST"})
	 */
	public function addToBasket(Product $product): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');
		$user = $this->getUser();

		if ($product->getQuantity() !== 0 && !$user->hasProductInBasket($product)) {
			$em = $this->getDoctrine()->getManager();

			$user->addProductToBasket($product);
			$em->flush();
		}

		return $this->render('product/show.html.twig', [
			'product' => $product,
		]);
	}

	/**
	 * @Route("/{id}/remove-from-basket", name="remove_product_from_basket", methods={"POST"})
	 */
	public function removeFromBasket(Product $product): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');

		$em = $this->getDoctrine()->getManager();

		$user = $this->getUser();
		$basketItem = $user->getBasketItemFromProduct($product);

		if (!is_null($basketItem)) {
			$user->removeItemFromBasket($basketItem);
			$em->flush();
		}

		return $this->render('product/show.html.twig', [
			'product' => $product,
		]);
	}

	public static function getShippingPrice(int $orderPrice): float
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

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

		if (!$user->hasProductInBasket($product)) {
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
}

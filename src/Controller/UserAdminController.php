<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Entity\UserBasket;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class UserAdminController extends AbstractController
{
	private $session;

	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * @Route("/", name="admin_user_index", methods={"GET"})
	 */
	public function index(UserRepository $userRepository): Response
	{
		return $this->render('user/admin/index.html.twig', [
			'users' => $userRepository->findAll(),
		]);
	}

	protected function render(string $view, array $parameters = [], ?Response $response = null): Response
	{
		// Always add categories for navbar on render
		$categoryRepository = $this->getDoctrine()->getRepository(ProductCategory::class);
		$parameters['categories'] = $categoryRepository->findAll();

		// Always add session basket length
		$basket = UserBasket::getBasketFromSession($this->session);
		$parameters['basketLength'] = count($basket);

		return parent::render($view, $parameters, $response);
	}
}

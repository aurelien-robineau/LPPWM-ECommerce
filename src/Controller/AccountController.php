<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\AccountEditType;
use App\Form\PasswordChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * @Route("/edit", name="account_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(AccountEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/change-password", name="account_password_change", methods={"GET","POST"})
     */
    public function changePassword(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(PasswordChangeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->flush();
            return $this->redirectToRoute('account');
        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView()
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

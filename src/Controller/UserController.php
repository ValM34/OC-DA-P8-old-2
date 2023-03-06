<?php

namespace App\Controller;

use App\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
  public function __construct(private EntityManagerInterface $entityManager)
  {}

  /**
   * @Route("/users", name="user_list")
   */
  public function listAction()
  {
    return $this->render('user/list.html.twig', ['users' => $this->entityManager->getRepository(User::class)->findAll()]);
  }

  /**
   * @Route("/users/create", name="user_create")
   */
  public function createAction(Request $request)
  {
    $user = new User();
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isValid()) {
      $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
      $user->setPassword($password);

      $this->entityManager->persist($user);
      $this->entityManager->flush();

      $this->addFlash('success', "L'utilisateur a bien été ajouté.");

      return $this->redirectToRoute('user_list');
    }

    return $this->render('user/create.html.twig', ['form' => $form->createView()]);
  }

  /**
   * @Route("/users/{id}/edit", name="user_edit")
   */
  public function editAction(User $user, Request $request)
  {
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isValid()) {
      $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
      $user->setPassword($password);

      $this->entityManager->flush();

      $this->addFlash('success', "L'utilisateur a bien été modifié");

      return $this->redirectToRoute('user_list');
    }

    return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
  }
}

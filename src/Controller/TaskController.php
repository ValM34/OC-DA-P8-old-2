<?php

namespace App\Controller;

use App\Entity\Task;
use AppBundle\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\TaskServiceInterface;

class TaskController extends AbstractController
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private TaskServiceInterface $taskService
  )
  {}

  /**
   * @Route("/tasks", name="task_list")
   */
  public function listAction()
  {
    return $this->render('task/list.html.twig', ['tasks' => $this->entityManager->getRepository(Task::class)->findAll()]);
  }

  /**
   * @Route("/tasks/create", name="task_create")
   */
  public function createAction(Request $request)
  {
    $task = new Task();
    $form = $this->createForm(TaskType::class, $task);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->taskService->create($task);

      $this->addFlash('success', 'La tâche a été bien été ajoutée.');

      return $this->redirectToRoute('task_list');
    }

    return $this->render('task/create.html.twig', ['form' => $form->createView()]);
  }

  /**
   * @Route("/tasks/{id}/edit", name="task_edit")
   */
  public function editAction(Task $task, Request $request)
  {
    $form = $this->createForm(TaskType::class, $task);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->flush();
      $this->addFlash('success', 'La tâche a bien été modifiée.');

      return $this->redirectToRoute('task_list');
    }

    return $this->render('task/edit.html.twig', [
      'form' => $form->createView(),
      'task' => $task,
    ]);
  }

  // @TODO : il y a une erreur dans le code : le message flash doit 1/2 fois afficher que la tâche a été marquée comme non faite
  // et ce n'est actuellement pas le cas
  /**
   * @Route("/tasks/{id}/toggle", name="task_toggle")
   */
  public function toggleTaskAction(Task $task)
  {
    $task->toggle(!$task->getIsDone());
    $this->taskService->update($task);

    $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

    return $this->redirectToRoute('task_list');
  }

  /**
   * @Route("/tasks/{id}/delete", name="task_delete")
   */
  public function deleteTaskAction(Task $task)
  {
    $this->entityManager->remove($task);
    $this->entityManager->flush();

    $this->addFlash('success', 'La tâche a bien été supprimée.');

    return $this->redirectToRoute('task_list');
  }
}

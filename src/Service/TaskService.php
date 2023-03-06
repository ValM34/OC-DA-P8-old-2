<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskService implements TaskServiceInterface
{
  private $dateTimeImmutable;

  public function __construct(private EntityManagerInterface $entityManager)
  {
    $this->dateTimeImmutable = new \DateTimeImmutable();
  }

  public function create(Task $task): void
  {
    $task->setCreatedAt($this->dateTimeImmutable);
    $this->entityManager->persist($task);
    $this->entityManager->flush();
  }

  public function update(Task $task): void
  {
    $this->entityManager->persist($task);
    $this->entityManager->flush();
  }
}

<?php

namespace App\Service;

use App\Entity\Task;

Interface TaskServiceInterface
{
  public function create(Task $task): void;
  public function update(Task $task): void;
}

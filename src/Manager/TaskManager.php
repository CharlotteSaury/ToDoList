<?php

namespace App\Manager;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TaskManager
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager, Security $security)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function handleListAction(bool $isDone = false)
    {
        return $this->taskRepository->findBy(['isDone' => $isDone]);
    }

    public function handleToggleAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();
        return $task;
    }

    public function handleCreateOrUpdate(Task $task = null)
    {
        if ($task != null) {
            $task->setAuthor($this->security->getUser());
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
    }

    public function handleDeleteAction(Task $task)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}

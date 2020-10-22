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
        try {
            return $this->taskRepository->findBy(['isDone' => $isDone]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function handleToggleAction(Task $task)
    {
        try {
            $task->toggle(!$task->isDone());
            $this->entityManager->flush();
            return $task;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function handleCreateOrUpdate(Task $task = null)
    {
        try {
            if ($task != null) {
                $task->setAuthor($this->security->getUser());
                $this->entityManager->persist($task);
            }
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function handleDeleteAction(Task $task)
    {
        try {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

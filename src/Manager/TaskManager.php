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

    /**
     * Handle task list recovery from database.
     *
     * @param bool $isDone
     *
     * @return array
     */
    public function handleListAction(bool $isDone = false)
    {
        return $this->taskRepository->findBy(['isDone' => $isDone]);
    }

    /**
     * Handle task status modification.
     *
     * @param Task $task
     *
     * @return Task $task
     */
    public function handleToggleAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        return $task;
    }

    /**
     * Handle task creation or edition in database.
     *
     * @param Task $task
     *
     * @return void
     */
    public function handleCreateOrUpdate(Task $task = null)
    {
        if (null !== $task) {
            $task->setAuthor($this->security->getUser());
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
    }

    /**
     * Handle task deletion in database.
     *
     * @param Task $task
     *
     * @return void
     */
    public function handleDeleteAction(Task $task)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}

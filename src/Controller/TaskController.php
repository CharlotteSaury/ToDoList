<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Manager\TaskManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    public function __construct(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
    }

    /**
     * Manage to do task list display.
     *
     * @Route("/tasks/todo", name="task_todo_list")
     *
     * @return Response
     */
    public function listAction()
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskManager->handleListAction(),
            ]
        );
    }

    /**
     * Manage done task list display.
     *
     * @Route("/tasks/done", name="task_done_list")
     *
     * @return Response
     */
    public function doneListAction()
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskManager->handleListAction(true),
            'type' => 'done',
            ]
        );
    }

    /**
     * Manage new task creation.
     *
     * @Route("/tasks/create", name="task_create")
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskManager->handleCreateOrUpdate($task);
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_todo_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Manage existing task edition.
     *
     * @Route("/tasks/{id}/edit", name="task_edit")
     *
     * @return Response
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskManager->handleCreateOrUpdate();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_todo_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * Manage task status modification (done/todo).
     *
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     *
     * @return Response
     */
    public function toggleTaskAction(Task $task)
    {
        $task = $this->taskManager->handleToggleAction($task);
        $status = (true === $task->isDone()) ? 'faite' : 'non terminée';
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme '.$status, $task->getTitle()));

        return $this->redirectToRoute('task_todo_list');
    }

    /**
     * Manage task deletion restricted to task author or admin for anonymous tasks.
     *
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @IsGranted("TASK_DELETE", subject="task", statusCode=401)
     *
     * @return Response
     */
    public function deleteTaskAction(Task $task)
    {
        $this->taskManager->handleDeleteAction($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_todo_list');
    }
}

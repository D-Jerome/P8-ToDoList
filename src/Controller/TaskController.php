<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    /**
     * Display the list of tasks that must be done
     *
     * @param TaskRepository $taskRepository param
     * @param User           $connectedUser
     */
    #[Route(path: '/tasks', name: 'task_list')]
    public function list(TaskRepository $taskRepository, #[CurrentUser] User $connectedUser): Response
    {
        if($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => false])]);
        }

        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $connectedUser, 'isDone' => false])]);
    }

    /**
     * Display the list of tasks that have been completed
     *
     * @param TaskRepository $taskRepository param
     * @param User           $connectedUser
     */
    #[Route(path: '/tasks/done', name: 'task_list_done')]
    public function listDone(TaskRepository $taskRepository, #[CurrentUser] User $connectedUser): Response
    {
        if($connectedUser->getRoles() === ['ROLE_ADMIN']) {
            return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => true])]);
        }

        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $connectedUser, 'isDone' => true])]);
    }

    /**
     * Manage the form & pages to create a task
     *
     * @param User $connectedUser
     */
    #[Route(path: '/tasks/create', name: 'task_create')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        #[CurrentUser] User $connectedUser): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($connectedUser);
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Manage the form & pages to edit a task
     */
    #[Route(path: '/tasks/{id}/edit', name: 'task_edit')]
    #[IsGranted('TASK_MODIFY', subject: 'task')]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * Manage the feature to change task status (done or not)
     */
    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle')]
    #[IsGranted('TASK_MODIFY', subject: 'task')]
    public function toggleTask(Task $task, EntityManagerInterface $em): Response
    {
        $task->toggle(!$task->isDone());
        $em->flush();
        if ($task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

            return $this->redirectToRoute('task_list');
        }

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list_done');
    }

    /**
     * Manage the feature to delete a task
     */
    #[Route(path: '/tasks/{id}/delete', name: 'task_delete')]
    #[IsGranted('TASK_DELETE', subject: 'task')]
    public function deleteTask(Task $task, EntityManagerInterface $em): Response
    {
        // $this->denyAccessUnlessGranted('TASK_DELETE', $task);

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}

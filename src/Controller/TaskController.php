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

class TaskController extends AbstractController
{
    #[Route(path: '/tasks', name: 'task_list')]
    public function list(TaskRepository $taskRepository, #[CurrentUser] User $connectedUser): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $connectedUser, 'isDone' => false])]);
    }

    #[Route(path: '/tasks/done', name: 'task_list_done')]
    public function listDone(TaskRepository $taskRepository, #[CurrentUser] User $connectedUser): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $connectedUser, 'isDone' => true])]);
    }

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

    #[Route(path: '/tasks/{id}/edit', name: 'task_edit')]
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

    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle')]
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

    #[Route(path: '/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('TASK_DELETE', $task);

        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');
        if ($task->isDone()) {
            return $this->redirectToRoute('task_list_done');
        }

        return $this->redirectToRoute('task_list');
    }
}

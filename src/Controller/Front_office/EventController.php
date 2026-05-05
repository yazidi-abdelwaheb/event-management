<?php

namespace App\Controller\Front_office;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/my-profile/events')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $events = $eventRepository->findBy(
            ['organizer' => $this->getUser()],
            ['created_at' => 'DESC']
        );
        return $this->render('Front_office/my-profile/event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , SluggerInterface $slugger): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile instanceof UploadedFile) {
                try {
                    $event->setImage($this->uploadImage($imageFile, $slugger));
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Image upload failed: ' . $e->getMessage());
                    return $this->redirectToRoute('app_event_new');
                }
            }

            $event->setOrganizer($this->getUser());
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Event "' . $event->getTitle() . '" created successfully!');
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front_office/my-profile/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
         if ($event->getOrganizer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot view this event.');
        }
        return $this->render('Front_office/my-profile/event/show.html.twig', [
            'event' => $event,
            'subscribers' => $event->getEventSubscribes(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager , SluggerInterface $slugger): Response
    {
        if ($event->getOrganizer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this event.');
        }
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile instanceof UploadedFile) {
                $this->removeOldImage($event->getImage());

                try {
                    $event->setImage($this->uploadImage($imageFile, $slugger));
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Image upload failed: ' . $e->getMessage());
                    return $this->redirectToRoute('app_event_edit', ['id' => $event->getId()]);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Event "' . $event->getTitle() . '" updated successfully!');
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front_office/my-profile/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->removeOldImage($event->getImage());
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    private function uploadImage(UploadedFile $imageFile, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        $imageFile->move($this->getParameter('events_images_directory'), $newFilename);

        return $newFilename;
    }

    private function removeOldImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $oldPath = $this->getParameter('events_images_directory') . '/' . $filename;
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }
}

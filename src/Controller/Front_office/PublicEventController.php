<?php

namespace App\Controller\Front_office;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\EventSubscribe;
use App\Form\CommentType;
use App\Form\EventSubscribeType;
use App\Repository\EventRepository;
use App\Repository\EventSubscribeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

final class PublicEventController extends AbstractController
{
    #[Route('/event', name: 'app_public_event')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $page  = max(1, $request->query->getInt('page', 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $events      = $eventRepository->findAllMinContentPaginated($limit, $offset);
        $totalEvents = $eventRepository->count([]);
        $totalPages  = (int) ceil($totalEvents / $limit);

        return $this->render('Front_office/public_event/index.html.twig', [
            'events'       => $events,
            'total_pages'  => $totalPages,
            'current_page' => $page,
            'total_events' => $totalEvents,
        ]);
    }



    #[Route('/event/{id}', name: 'app_public_event_show', methods: ['GET', 'POST'])]
    public function show(
        Event $event,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $comment = new Comment();
        $form    = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEvent($event);
            $comment->setUser($this->getUser()? $this->getUser() : null);
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Comment added successfully!');
            return $this->redirectToRoute('app_public_event_show', ['id' => $event->getId()]);
        }

        return $this->render('Front_office/public_event/show.html.twig', [
            'event'        => $event,
            'commentForm'  => $form,
            'comments'     => $event->getComments(), // ← relation OneToMany
        ]);
    }

    #[Route('/event/{id}/subscribe', name: 'app_public_event_subscribe')]
    public function subscribe(Event $event, Request $request, EventSubscribeRepository $eventSubscribeRepository, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $subscription = new EventSubscribe();
        $subscription->setEvent($event);

        $form = $this->createForm(EventSubscribeType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($eventSubscribeRepository->findOneBy(['event' => $event, 'email' => $subscription->getEmail()])) {
                $this->addFlash('error', 'You are already subscribed to this event with this email.');
                return $this->redirectToRoute('app_public_event_show', ['id' => $event->getId()]);
            }

            $em->persist($subscription);
            $em->flush();

            $qrData = json_encode([
                'subscription_id' => $subscription->getId(),
                'event_id'        => $event->getId(),
                'subscriber'      => $subscription->getEmail(),
            ]);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrData);

            $email = (new TemplatedEmail())
                ->from(new Address($this->getParameter('mailer_from_email'), $this->getParameter('mailer_from_name')))
                ->to($subscription->getEmail())
                ->subject('✅ Confirmation de votre inscription — ' . $event->getTitle())
                ->htmlTemplate('Front_office/public_event/event_subscribe/_confirmation_email.html.twig')
                ->context([
                    'subscription' => $subscription,
                    'event'        => $event,
                    'qrUrl'        => $qrUrl,
                ]);

            try {
                $mailer->send($email);
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'Subscription saved, but confirmation email could not be sent at this time.');
                return $this->redirectToRoute('app_public_event_subscription_success', ['id' => $subscription->getId()]);
            }

            return $this->redirectToRoute('app_public_event_subscription_success', ['id' => $subscription->getId()]);
        }

        return $this->render('Front_office/public_event/event_subscribe/index.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/event/{eventId}/subscribe/{id}/success', name: 'app_public_event_subscription_success')]
    public function success(EventSubscribe $subscription): Response
    {
        return $this->render('Front_office/public_event/event_subscribe/success.html.twig', [
            'sub' => $subscription,
            'event' => $subscription->getEvent(),
        ]);
    }
}

<?php

namespace App\Controller\Front_office;

use App\Entity\Event;
use App\Entity\EventSubscribe;
use App\Form\EventSubscribeType;
use App\Repository\EventRepository;
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

    #[Route('/event/{id}', name: 'app_public_event_show')]
    public function show(Event $event): Response
    {
        return $this->render('Front_office/public_event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/{id}/subscribe', name: 'app_public_event_subscribe')]
    public function subscribe(int $id, EventRepository $eventRepo, Request $request, EntityManagerInterface $em , MailerInterface $mailer): Response
    {
        $eventData = $eventRepo->findOneMinContent($id);

        if (!$eventData) {
            throw $this->createNotFoundException("Event not found.");
        }

        $subscription = new EventSubscribe();
        $eventObject = $em->getRepository(Event::class)->find($id);
        $subscription->setEvent($eventObject);

        $form = $this->createForm(EventSubscribeType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$subscription->setStatus('pending');
            $em->persist($subscription);
            $em->flush();

            $qrData = json_encode([
                'subscription_id' => $subscription->getId(),
                'event_id'        => $id,
                'subscriber'      => $subscription->getEmail(), // adaptez selon votre entité
            ]);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrData);

            $email = (new TemplatedEmail())
                ->from(new Address('yazidiabdelwaheb@gmail.com', 'EventManage'))
                ->to($subscription->getEmail())
                ->subject('✅ Confirmation de votre inscription — ' . $eventObject->getTitle())
                ->htmlTemplate('Front_office/public_event/event_subscribe/_confirmation_email.html.twig')
                ->context([
                    'subscription' => $subscription,
                    'event'        => $eventObject,
                    'qrUrl'        => $qrUrl,
                ]);

            $mailer->send($email);


            return $this->redirectToRoute('app_public_event_subscription_success', [
                'eventId' => $id,
                'id' => $subscription->getId(),
            ]);
        }

        return $this->render('/Front_office/public_event/event_subscribe/index.html.twig', [
            'event' => $eventData,
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

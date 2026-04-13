<?php

namespace App\Controller\Front_office;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicEventController extends AbstractController
{
    #[Route('/event', name: 'app_public_event')]
    public function index(Request $request , EventRepository $eventRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 6;
        $offset = ($page - 1) * $limit;

       

        $events = $eventRepository->getAllMinContent($offset, $limit);

        $totalEvents = count($eventRepository->findAll());
        $totalPages = ceil($totalEvents / $limit);

        
        return $this->render('front_office/public_event/index.html.twig', [
            'controller_name' => 'PublicEventController',
            'events' => $events,
            'total_pages' => $totalPages,
            'current_page' => $page,
        ]);
    }
}

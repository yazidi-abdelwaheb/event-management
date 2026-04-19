<?php

namespace App\Controller\Front_office;

use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('', name: 'app_home_page')]
    public function index(CategoryRepository $categoryRepository , EventRepository $eventRepository): Response
    {
        $categories = $categoryRepository->findAllMinContentPaginated(8, 0);

        $events = $eventRepository->findAllMinContentPaginated(6, 0);

        return $this->render('Front_office/home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'categories' => $categories,
            'events' => $events,
            'calendar_events' => $eventRepository->findAllForCalendar(),
        ]);
    }

    #[Route('/contact', name: 'app_home_page_contact')]
    public function contact(): Response
    {
        return $this->render('Front_office/home_page/contact.html.twig');
    }

    #[Route('/about', name: 'app_home_page_about')]
    public function about(): Response
    {
        return $this->render('Front_office/home_page/about.html.twig');
    }
}

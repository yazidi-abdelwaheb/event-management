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
        $categories = $categoryRepository->createQueryBuilder('c')
            ->select('c.id, c.label, c.image, COUNT(e.id) as eventCount')
            ->leftJoin('c.events', 'e')
            ->groupBy('c.id')
            ->orderBy('eventCount', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        $events = $eventRepository->createQueryBuilder('e')
            ->select('e.id, e.title, e.image, e.start_date_time,e.end_date_time, e.location, e.price, c.label as category')
            ->join('e.category', 'c')
            ->orderBy('e.created_at', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        return $this->render('Front_office/home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'categories' => $categories,
            'events' => $events,
        ]);
    }
}

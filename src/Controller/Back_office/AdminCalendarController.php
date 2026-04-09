<?php
namespace App\Controller\Back_office;

use App\Repository\EventRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AdminCalendarController extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    #[Route('/admin/calendar', name: 'admin_calendar')]
    public function index(): Response
    {
        return $this->render('back_office/events/admin_calendar/index.html.twig', [
            'events' => $this->eventRepository->findAll()
        ]);
    }
}
<?php

namespace App\Controller\Front_office;

use App\Repository\EventSubscribeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyProfileController extends AbstractController
{
    #[Route('/my-profile', name: 'app_my_profile')]
    public function index(): Response
    {
        return $this->render('Front_office/my-profile/client-profile/index.html.twig', [
            'controller_name' => 'MyProfileController',
        ]);
    }

    #[Route('/my-profile/tickets', name: 'app_my_profile_tickets')]
    public function tickets(EventSubscribeRepository $eventSubscribeRepository): Response
    {
        $eventSubscribe = $eventSubscribeRepository->findBy(['email' => $this->getUser()->getEmail()]);

        return $this->render('Front_office/my-profile/tickets/index.html.twig', [
            'tickets' => $eventSubscribe,
        ]);
    }
}

<?php

namespace App\Controller\Front_office;

use App\Entity\Contact;
use App\Entity\Newsletter;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\NewsletterRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/contact', name: 'app_home_page_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request,
        EntityManagerInterface $em,
        EmailService $emailService
    ): Response
    {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('contact_form', $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid form submission. Please try again.');
                return $this->redirectToRoute('app_home_page_contact');
            }

            $firstName = trim((string) $request->request->get('first_name', ''));
            $lastName  = trim((string) $request->request->get('last_name', ''));
            $email     = trim((string) $request->request->get('email', ''));
            $subject   = trim((string) $request->request->get('subject', ''));
            $message   = trim((string) $request->request->get('message', ''));

            if ($firstName === '' || $lastName === '' || $email === '' || $subject === '' || $message === '') {
                $this->addFlash('error', 'Please fill in all required fields.');
                return $this->redirectToRoute('app_home_page_contact');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');
                return $this->redirectToRoute('app_home_page_contact');
            }

            $contact = new Contact();
            $contact->setFirstName($firstName);
            $contact->setLastName($lastName);
            $contact->setEmail($email);
            $contact->setSubject($subject);
            $contact->setMessage($message);

            $em->persist($contact);
            $em->flush();

            try {
                $emailService->sendContactConfirmation($contact);
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Unable to send confirmation email right now. Your message was received.');
                return $this->redirectToRoute('app_home_page_contact');
            }

            $this->addFlash('success', 'Message sent successfully!');
            return $this->redirectToRoute('app_home_page_contact');
        }

        return $this->render('Front_office/home_page/contact.html.twig');
    }



   

    #[Route('/about', name: 'app_home_page_about')]
    public function about(): Response
    {
        return $this->render('Front_office/home_page/about.html.twig');
    }
}

<?php

namespace App\Controller\Front_office;

use App\Entity\Contact;
use App\Entity\Newsletter;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\NewsletterRepository;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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

    #[Route('/contact', name: 'app_home_page_contact')]
    public function contact(Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response
    {
        if ($request->isMethod('POST')) {

        $firstName = $request->request->get('first_name');
        $lastName  = $request->request->get('last_name');
        $email     = $request->request->get('email');
        $subject   = $request->request->get('subject');
        $message   = $request->request->get('message');

        $contact = new Contact();
        $contact->setFirstName($firstName);
        $contact->setLastName($lastName);
        $contact->setEmail($email);
        $contact->setSubject($subject);
        $contact->setMessage($message);

        $em->persist($contact);
        $em->flush();

        
        $userEmail = (new TemplatedEmail())
           ->from(new Address('yazidiabdelwaheb@gmail.com', 'Team Event Management'))
            ->to((string) $email)
            ->subject('We received your message')
            ->htmlTemplate('Front_office/home_page/_contact_user.html.twig')
            ->context([
                'contact' => $contact
            ]);

        $mailer->send($userEmail);

        $this->addFlash('success', 'Message sent successfully!');

        return $this->redirectToRoute('app_home_page_contact');
    }
        return $this->render('Front_office/home_page/contact.html.twig');
    }



   /* #[Route('/newsletter', name: 'app_home_page_newsletter', methods: ['GET'])]
    public function newsletter(
        NewsletterRepository $newsRepo, 
        Request $request,
        ValidatorInterface $validator
    ): Response
    {
        $email = $request->request->get('email');
        
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Please enter a valid email address.');
            return $this->redirectToRoute('app_home_page');
        }
        
        
        if ($newsRepo->findOneBy(['email' => $email])) {
            $this->addFlash('info', 'This email is already subscribed.');
            return $this->redirectToRoute('app_home_page');
        }
        
        try {
            $news = new Newsletter();
            $news->setEmail($email);
            
            // Validate entity
            $errors = $validator->validate($news);
            if (count($errors) > 0) {
                $this->addFlash('error', 'Invalid email address.');
                return $this->redirectToRoute('app_home_page');
            }
            
            $newsRepo->save($news, true);
            $this->addFlash('success', 'Successfully subscribed to newsletter!');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred. Please try again.');
        }
        
        return $this->redirectToRoute('app_home_page');
    }*/

    #[Route('/about', name: 'app_home_page_about')]
    public function about(): Response
    {
        return $this->render('Front_office/home_page/about.html.twig');
    }
}

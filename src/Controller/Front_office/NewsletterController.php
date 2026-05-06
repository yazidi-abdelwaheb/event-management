<?php

namespace App\Controller\Front_office;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_home_page_newsletter', methods: ['POST'])]
    public function newsletter(
        NewsletterRepository $newsRepo,
        EntityManagerInterface $entityManager,
        Request $request,
        ValidatorInterface $validator,
        EmailService $emailService
    ): Response
    {
        // Vérification CSRF
        if (!$this->isCsrfTokenValid('newsletter_form', $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid form submission. Please try again.');
            return $this->redirectToRoute('app_home_page');
        }

        $email = trim((string) $request->request->get('email', ''));

        if (empty($email)) {
            $this->addFlash('error', 'Email address is required.');
            return $this->redirectToRoute('app_home_page');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Please enter a valid email address.');
            return $this->redirectToRoute('app_home_page');
        }

        if ($newsRepo->findByEmail($email)) {
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

            $entityManager->persist($news);
            $entityManager->flush();

            // Envoi d'email de confirmation
            try {
                $emailService->sendNewsletterConfirmation($news);
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'Subscription saved, but confirmation email could not be sent at this time.');
            }

            $this->addFlash('success', 'Successfully subscribed to newsletter!');

        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred. Please try again.');
        }

        return $this->redirectToRoute('app_home_page');
    }
}

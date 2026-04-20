<?php

namespace App\Controller\Front_office;

use App\Entity\User;
use App\Repository\EventSubscribeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/my-profile/edit', name: 'app_my_profile_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $error = null;
        $success = null;

        if ($request->isMethod('POST')) {
            $section = $request->request->get('section');
            

            // ── Section : Infos Personnelles ──
            if ($section === 'info') {
                $firstName = trim($request->request->get('first_name'));
                $lastName  = trim($request->request->get('last_name'));
                $email     = trim($request->request->get('email'));

                if (!$firstName || !$lastName || !$email) {
                    $error = 'All fields are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } else {
                    $user->setFirstName($firstName);
                    $user->setLastName($lastName);
                    $user->setEmail($email);
                    $em->flush();
                    $success = 'Profile information updated successfully.';
                }
            }

            // ── Section : Avatar ──
            if ($section === 'avatar') {
                $avatarFile = $request->files->get('avatar');
                if ($avatarFile) {
                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($avatarFile->getMimeType(), $allowed)) {
                        $error = 'Unsupported format (JPG, PNG, WEBP only).';
                    } elseif ($avatarFile->getSize() > 2 * 1024 * 1024) {
                        $error = 'Image too large (max 2MB).';
                    } else {
                        // Optionnel : supprimer l'ancien avatar s'il existe
                        $filename = uniqid() . '.' . $avatarFile->guessExtension();
                        $avatarFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/avatars', $filename);
                        $user->setAvatar($filename);
                        $em->flush();
                        $success = 'Avatar updated successfully.';
                    }
                }
            }

            // ── Section : Mot de Passe ──
            if ($section === 'password') {
                $current = $request->request->get('current_password');
                $new     = $request->request->get('new_password');
                $confirm = $request->request->get('confirm_password');

                if (!$hasher->isPasswordValid($user, $current)) {
                    $error = 'Current password is incorrect.';
                } elseif (strlen($new) < 8) {
                    $error = 'New password must be at least 8 characters.';
                } elseif ($new !== $confirm) {
                    $error = 'Passwords do not match.';
                } else {
                    $user->setPassword($hasher->hashPassword($user, $new));
                    $em->flush();
                    $success = 'Password updated successfully.';
                }
            }

            // Utilisation des Flash Messages (plus propre pour le design)
            if ($success) $this->addFlash('success', $success);
            if ($error) $this->addFlash('error', $error);

            return $this->redirectToRoute('app_my_profile_edit');
        }

        return $this->render('Front_office/my-profile/client-profile/edit.html.twig', [
            'user' => $user,
            'error' => $error,
            'success' => $success

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

<?php
namespace App\Controller\Back_office;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminProfileController extends AbstractController
{
    #[Route('/admin/profile', name: 'admin_user_profile')]
    #[IsGranted('ROLE_ADMIN')]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $error = null;
        $success = null;

        if ($request->isMethod('POST')) {
            $section = $request->request->get('section');

            // ── Section : infos personnelles ──
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
                    $success = 'Information updated successfully.';
                }
            }

            // ── Section : avatar ──
            if ($section === 'avatar') {
                $avatarFile = $request->files->get('avatar');
                if ($avatarFile) {
                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($avatarFile->getMimeType(), $allowed)) {
                        $error = 'Format non supporté. Utilisez JPG, PNG ou WEBP.';
                    } elseif ($avatarFile->getSize() > 2 * 1024 * 1024) {
                        $error = 'Image trop lourde (max 2 Mo).';
                    } else {
                        $filename = uniqid() . '.' . $avatarFile->guessExtension();
                        $avatarFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/avatars', $filename);
                        $user->setAvatar($filename);
                        $em->flush();
                        $success = 'Avatar updated successfully.';
                    }
                }
            }

            // ── Section : mot de passe ──
            if ($section === 'password') {
                $current  = $request->request->get('current_password');
                $new      = $request->request->get('new_password');
                $confirm  = $request->request->get('confirm_password');

                if (!$hasher->isPasswordValid($user, $current)) {
                    $error = 'Mot de passe actuel incorrect.';
                } elseif (strlen($new) < 8) {
                    $error = 'new password must be at least 8 characters long.';
                } elseif ($new !== $confirm) {
                    $error = 'The passwords do not match.';
                } else {
                    $user->setPassword($hasher->hashPassword($user, $new));
                    $em->flush();
                    $success = 'Password updated successfully.';
                }
            }
        }

        return $this->render('back_office/profile/index.html.twig', [
            'user'    => $user,
            'error'   => $error,
            'success' => $success,
        ]);
    }
}
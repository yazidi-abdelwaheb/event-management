<?php
namespace App\Controller\Back_office;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminLoginController extends AbstractController
{
    #[Route('/admin/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error'                => $authenticationUtils->getLastAuthenticationError(),
            'last_username'        => $authenticationUtils->getLastUsername(),
            'page_title'           => 'Event Management - Admin Panel',
            'csrf_token_intention' => 'authenticate',
            'target_path'          => $this->generateUrl('admin'),
            'username_label'       => 'Email',
            'password_label'       => 'Password',
            'sign_in_label'        => 'Sign In',
            'username_parameter'   => '_username',
            'password_parameter'   => '_password',
            'forgot_password_enabled' => false,
        ]);
    }

    #[Route('/admin/logout', name: 'admin_logout')]
    public function logout(): void {}
}
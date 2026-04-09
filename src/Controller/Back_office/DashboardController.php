<?php

namespace App\Controller\Back_office;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Back_office\UserCrudController;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    /*#[Route('/admin/dashboard', name: 'admin_custom_dashboard')]
    public function customDashboard(UserRepository $userRepo, EventRepository $eventRepo): Response
    {
        $usersCount = $userRepo->count([]);
        $eventsCount = $eventRepo->count([]);

        return $this->render('back_office/dashboard/index.html.twig', [
            'usersCount'  => $usersCount,
            'eventsCount' => $eventsCount,
        ]);
    }*/

    public function index(): Response
    {
        return $this->render('back_office/dashboard/index.html.twig');
    }


    // ── Logout ──
    #[Route('/admin/logout', name: 'admin_logout')]
    public function logout(): void {}


   public function configureUserMenu(UserInterface $user): UserMenu
    {
        /** @var \App\Entity\User $user */
        
        $avatarUrl = $user->getAvatar() 
            ? '/uploads/avatars/' . $user->getAvatar()  
            : null;

        $menu = parent::configureUserMenu($user)
            ->setName($user->getFirstName() . ' ' . $user->getLastName())
            ->displayUserName(true)
            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', 'admin_user_profile'),
                MenuItem::section(''),
            ]);

        if ($avatarUrl) {
            $menu->setAvatarUrl($avatarUrl);
        } else {
            $menu->setGravatarEmail($user->getEmail());
        }

        return $menu;
    }
     

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Event Management');
            
    }

    public function configureMenuItems(): iterable
    {   
        yield MenuItem::section('Analytics & Stats');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Administration management');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fas fa-users');
        yield MenuItem::linkTo(RoleCrudController::class, 'Roles', 'fas fa-user-tag');
        yield MenuItem::section('Event management');
        yield MenuItem::linkTo(EventCrudController::class, 'Events', 'fas fa-calendar');
        yield MenuItem::linkToRoute('Calendar', 'fa fa-calendar-alt', 'admin_calendar');
    }
}
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
use App\Repository\UserRepository;
use App\Repository\EventRepository;
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

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getUserIdentifier())
            // use this method if you don't want to display the name of the user
            ->displayUserName(false)

            // you can return an URL with the avatar image
            ->setAvatarUrl('https://...')
            ->setAvatarUrl($user->getUserIdentifier())
            // use this method if you don't want to display the user image
            ->displayUserAvatar(false)
            // you can also pass an email address to use gravatar's service
            ->setGravatarEmail($user->getUserIdentifier())

            // you can hide the "Sign out" link from the user menu (e.g. when using
            // authentication methods like HTTP Basic or OAuth that don't support logout)
            ->disableLogoutLink()

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Event Management')
            ->renderContentMaximized()
            ->renderSidebarMinimized()
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {   
        yield MenuItem::section('Analytics & Stats');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Administration management');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fas fa-users');
        yield MenuItem::section('Event management');
        yield MenuItem::linkTo(EventCrudController::class, 'Events', 'fas fa-calendar');
    }
}
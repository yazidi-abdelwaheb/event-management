<?php

namespace App\Controller\Back_office;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactCrudController extends AbstractController
{
   
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    #[Route('/admin/inbox', name: 'admin_inbox')]
    public function index(): Response
    {
        return $this->render('back_office/contact/index.html.twig', [
            'messages' => $this->contactRepository->findBy([], ['created_at' => 'DESC'])
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', '📩 Inbox Messages')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['firstName', 'lastName', 'email', 'subject']);
    }

   
}

<?php

namespace App\Controller\Back_office;

use App\Entity\EventSubscribe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventSubscribeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EventSubscribe::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('full_name'),
            TextField::new('email'),
            TextField::new('phone'),
            DateField::new('created_at')->hideOnForm(),
            AssociationField::new('event'),
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Subscription')
            ->setEntityLabelInPlural('Subscriptions')
            ->setPageTitle('index', 'Event Subscriptions management')
            ->setPaginatorPageSize(5)
            ->setPaginatorRangeSize(2)
            ->setPaginatorFetchJoinCollection(true)
            ->setPaginatorUseOutputWalkers(true);
    }

     public function configureActions(Actions $actions): Actions
    {
        
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
            
    }

     public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('created_at')
            ->add('event');
    }
}

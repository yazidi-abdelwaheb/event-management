<?php

namespace App\Controller\Back_office;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    
    public function configureFields(string $pageName): iterable
{
    return [

        IdField::new('id')
            ->onlyOnIndex(),
        ImageField::new('image')
            ->setBasePath('/uploads/events')
            ->setUploadDir('public/uploads/events')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false),

        TextField::new('title')
            ->setRequired(true),

        TextareaField::new('description')
            ->hideOnIndex(),

        DateTimeField::new('start_date_time', 'Start Date')
            ->setRequired(true),
        DateTimeField::new('end_date_time', 'End Date')
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('End date must be after start date'),

        TextField::new('location'),

        AssociationField::new('category')
            ->setRequired(true)
            ->setHelp('Select a Category for the Event'),

        IntegerField::new('capacity'),

        Field::new('subscribedCount')
            ->setLabel('Subscribed')
            ->hideOnForm(),

        MoneyField::new('price')
            ->setNumDecimals(3)
            ->setCurrency('TND')
            ->setStoredAsCents(false),

        AssociationField::new('organizer')
            ->setRequired(true)
            ->autocomplete(),

        DateTimeField::new('created_at')
            ->hideOnForm()->hideOnIndex(),

        DateTimeField::new('updated_at')
            ->hideOnForm()->hideOnIndex(),

        AssociationField::new('eventSubscribes')
            ->onlyOnDetail()
            ->setTemplatePath('back_office/events/event_subscribes/event_subscribes_list.html.twig'),
    ];
}
    


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Event')
            ->setEntityLabelInPlural('Events')
            ->setPageTitle('index', 'Events management')
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
            ->add('start_date_time')
            ->add('end_date_time')
            ->add('category')
            ->add('capacity')
            ->add('price')
            ->add('organizer');
    }
}

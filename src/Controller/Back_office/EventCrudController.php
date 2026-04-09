<?php

namespace App\Controller\Back_office;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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

        TextField::new('location'),

        TextField::new('category')->hideOnIndex(),

        IntegerField::new('capacity'),

        AssociationField::new('organizer')
            ->setRequired(true)
            ->autocomplete(),

        DateTimeField::new('created_at')
            ->hideOnForm()->hideOnIndex(),

        DateTimeField::new('updated_at')
            ->hideOnForm()->hideOnIndex(),
    ];
}
    


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Event')
            ->setEntityLabelInPlural('Events')
            ->setPageTitle('index', 'Events management');
    }

     public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}

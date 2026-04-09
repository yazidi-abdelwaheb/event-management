<?php

namespace App\Controller\Back_office;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [

            
            IdField::new('id')
                ->hideOnForm(),

            TextField::new('first_name', 'First Name')
                ->setRequired(true),

            TextField::new('last_name', 'Last Name')
                ->setRequired(true),

            EmailField::new('email'),

            TextField::new('password')
                ->onlyOnForms()
                ->setHelp('Enter a secure password!'),

            
            ArrayField::new('roles')
                ->onlyOnIndex(),

            
            ImageField::new('avatar')
                ->setBasePath('/uploads/avatars')
                ->setUploadDir('public/uploads/avatars')
                ->setRequired(false),

            DateTimeField::new('created_at')
                ->hideOnForm(),
            ];
    }*/
    
}

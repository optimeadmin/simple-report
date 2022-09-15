<?php

namespace Optime\SimpleReport\Bundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CodeEditorType;
use Optime\SimpleReport\Bundle\Entity\SimpleReport;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SimpleReportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SimpleReport::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('slug'),
            CodeEditorField::new('query_string')->setLanguage('sql'),
            BooleanField::new('active'),
            ChoiceField::new('type')->setChoices([
                'Query String' => 'query_string',
                'Service' => 'service'
            ])
        ];
    }

}

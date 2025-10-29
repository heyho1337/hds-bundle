<?php

namespace App\Controller\Admin\Crud;

use App\Entity\MenuType;
use App\Service\Admin\CrudService;
use App\Service\Modules\LangService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use App\Service\Modules\TranslateService;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class MenuTypeCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly CrudService $crudService,
        private readonly LangService $langService,
        private readonly TranslateService $translateService,
    ) {}
    
    public static function getEntityFqcn(): string
    {
        return MenuType::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof MenuType) return;

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof MenuType) return;

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * on forms
         */
        yield FormField::addTab($this->translateService->translateSzavak("options"));
            yield TextField::new('service', $this->translateService->translateSzavak("service"))
                ->hideOnIndex();
            yield BooleanField::new('active',$this->translateService->translateSzavak("active"))
                ->renderAsSwitch(true)
                ->setFormTypeOptions(['data' => true])
                ->onlyOnForms();


        yield FormField::addTab($this->translateService->translateSzavak($this->langService->getDefaultObject()->getLangsName()));
            yield TextField::new('name_'.$this->langService->getDefault(), $this->translateService->translateSzavak("name"))
                ->hideOnIndex();

        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isLangsDefault()){
                yield FormField::addTab($this->translateService->translateSzavak($lang->getLangsName()));
                yield TextField::new('name_'.$lang->getLangsCode(), $this->translateService->translateSzavak("name"))
                    ->hideOnIndex();
            }
        }
        
        /**
         * index
         */
        yield TextField::new('name_'.$this->langService->getDefault(), $this->translateService->translateSzavak("name"))
            ->formatValue(function ($value, $entity) {
                $url = $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('edit')
                    ->setEntityId($entity->getId())
                    ->generateUrl();

                return sprintf('<a href="%s">%s</a>', $url, htmlspecialchars($value));
            })
            ->onlyOnIndex()
            ->renderAsHtml();
        yield TextField::new('service', $this->translateService->translateSzavak("service"))
                ->hideOnForm();
        yield DateField::new('created_at', $this->translateService->translateSzavak("created_at", "created"))->hideOnForm();
        yield DateField::new('modified_at',$this->translateService->translateSzavak("modified_at", "modified"))->hideOnForm();
        yield BooleanField::new('active', $this->translateService->translateSzavak("active"))
            ->renderAsSwitch(true)
            ->onlyOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig');
    }
}

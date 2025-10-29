<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Config;
use App\Service\Admin\CrudService;
use App\Service\Modules\ImageService;
use App\Entity\Category;
use App\Service\Modules\LangService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;


class ConfigCrudController extends AbstractCrudController
{

    private string $lang;

    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private ImageService $imageService,
        private readonly CrudService $crudService,
        private readonly LangService $langService,
        private readonly TranslateService $translateService,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
        $this->lang = $this->langService->getDefault();
        if($this->requestStack->getCurrentRequest()){
            $locale = $this->requestStack->getCurrentRequest()->getSession()->get('_locale');
            if($locale){
                $this->lang = $this->requestStack->getCurrentRequest()->getSession()->get('_locale');
                $this->translateService->setLangs($this->lang);
                $this->langService->setLang($this->lang);
            }
        }
    }
    
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Config) return;

        $this->setImages($entityInstance);

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Config) return;

        $this->setImages($entityInstance);

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        Category::setCurrentLang($this->lang);
        $this->getContext()->getRequest()->setLocale($this->lang);
        $this->translator->getCatalogue($this->lang);
        $this->translator->setLocale($this->lang);
        
        /**
         * on forms
         */
        yield FormField::addTab($this->translateService->translateSzavak("options"));
            yield AssociationField::new('schema_type', "Schema ".$this->translateService->translateSzavak("schema_type",'type'))
                ->setRequired(true)
                ->autocomplete()
                ->hideOnIndex()
                ->setFormTypeOption('attr', [
                    'data-config-target' => 'typeField',
                    'data-action' => 'change->config#changeType'
                ]);
            yield TextField::new('email', $this->translateService->translateSzavak("email"))
                ->hideOnIndex();
            yield TextField::new('address', $this->translateService->translateSzavak("address"))
                ->hideOnIndex();
            yield TextField::new('city', $this->translateService->translateSzavak("city"))
                ->hideOnIndex();
            yield TextField::new('zip_code', $this->translateService->translateSzavak("zip_code", "zip code"))
                ->hideOnIndex();
            yield TextField::new('phone', $this->translateService->translateSzavak("phone"))
                ->hideOnIndex();
            yield TextareaField::new('schema_text'.$this->langService->getDefault(), "Schema ".$this->translateService->translateSzavak("text","text"))
                ->setFormTypeOption('row_attr', ['data-config-target' => 'schemaTextRow'])
                ->hideOnIndex();
            yield Field::new('favicon', 'Favicon')
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms();
            yield Field::new('apple_icon', 'Apple icon')
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms();

        yield FormField::addTab($this->translateService->translateSzavak($this->langService->getDefaultObject()->getLangsName()));
            yield TextField::new('title_'.$this->langService->getDefault(), $this->translateService->translateSzavak("title"))->hideOnIndex();
            yield TextField::new('meta_desc_'.$this->langService->getDefault(), $this->translateService->translateSzavak("meta_desc","meta desc"))->hideOnIndex();
        
        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isLangsDefault()){
                yield FormField::addTab($this->translateService->translateSzavak($lang->getLangsName()));
                yield TextField::new('title_'.$lang->getLangsCode(), $this->translateService->translateSzavak("title"))->hideOnIndex();
                yield TextField::new('meta_desc_'.$lang->getLangsCode(), $this->translateService->translateSzavak("meta_desc","meta desc"))->hideOnIndex();
            }
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('admin/crud/config_crud_form_theme.html.twig')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->addFormTheme('admin/form/image_upload_with_preview.html.twig')
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig');
    }

    public function setImages($entityInstance){
        $favicon = $this->getContext()->getRequest()->files->get('Config')['favicon'] ?? null;
        $apple_icon = $this->getContext()->getRequest()->files->get('Config')['apple_icon'] ?? null;

        $this->imageService->setFile($favicon);
        $favicon_ico = $this->imageService->faviconUpload("favicon");
        $favicon_image = $this->imageService->simpleUpload("favicon");
        $entityInstance->setFavicon($favicon_ico);
        
        $this->imageService->setFile($apple_icon);
        $apple_icon = $this->imageService->simpleUpload("apple_image");
        $entityInstance->setAppleIcon($apple_icon);
    }
}

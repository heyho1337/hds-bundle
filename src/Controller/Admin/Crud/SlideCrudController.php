<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Slide;
use App\Service\Admin\CrudService;
use App\Service\Modules\ImageService;
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
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;


class SlideCrudController extends AbstractCrudController
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
        return Slide::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Slide) return;

        $file = $this->getContext()->getRequest()->files->get('Slide')['image'] ?? null;
        $this->imageService->processImage($file, $entityInstance,"slide");

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Slide) return;

        /** @var UploadedFile|null $file */
        $file = $this->getContext()->getRequest()->files->get('Slide')['image'] ?? null;
        $this->imageService->processImage($file, $entityInstance,"slide");

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {

        /**
         * on forms
         */
        yield FormField::addTab($this->translateService->translateSzavak("options"));
            yield Field::new('image', $this->translateService->translateSzavak("image"))
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms();
            yield BooleanField::new('active',$this->translateService->translateSzavak("active"))
                ->renderAsSwitch(true)
                ->setFormTypeOptions(['data' => true])
                ->onlyOnForms();
            yield AssociationField::new('target', $this->translateService->translateSzavak("target"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();

        yield FormField::addTab($this->translateService->translateSzavak($this->langService->getDefaultObject()->getLangsName()));
            yield TextField::new('name_'.$this->langService->getDefault(), $this->translateService->translateSzavak("name"))
                ->hideOnIndex();
            yield TextareaField::new('text_'.$this->langService->getDefault(), $this->translateService->translateSzavak("text"))->hideOnIndex();
            yield TextField::new('title_'.$this->langService->getDefault(), $this->translateService->translateSzavak("title"))->hideOnIndex();
            yield TextField::new('alt_'.$this->langService->getDefault(), $this->translateService->translateSzavak("alt"))->hideOnIndex();
            yield TextField::new('slug_'.$this->langService->getDefault(), $this->translateService->translateSzavak("url"))->hideOnIndex();
            /*yield Field::new('text_'.$this->langService->getDefault(), $this->translateService->translateSzavak("text"))
                ->setFormType(CKEditorType::class)
                ->onlyOnForms();
            */
        
        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isLangsDefault()){
                yield FormField::addTab($this->translateService->translateSzavak($lang->getLangsName()));
                yield TextField::new('name_'.$lang->getLangsCode(), $this->translateService->translateSzavak("name"))
                    ->hideOnIndex();
                yield TextareaField::new('text_'.$lang->getLangsCode(), $this->translateService->translateSzavak("text"))->hideOnIndex();
                yield TextField::new('title_'.$lang->getLangsCode(), $this->translateService->translateSzavak("title"))->hideOnIndex();
                yield TextField::new('alt_'.$lang->getLangsCode(), $this->translateService->translateSzavak("alt"))->hideOnIndex();
                yield TextField::new('slug_'.$lang->getLangsCode(), $this->translateService->translateSzavak("url"))->hideOnIndex();
                /*yield Field::new('text_'.$lang->getLangsCode(), $this->translateService->translateSzavak("text"))
                    ->setFormType(CKEditorType::class)
                    ->onlyOnForms();
                */
            }
        }
        
        /**
         * index
         */
        yield TextField::new('getIdAsString', $this->translateService->translateSzavak('identification'))
            ->onlyOnIndex()
            ->formatValue(function ($value, $entity) {
            $idString = $entity->getIdAsString();

            $url = $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction('edit')
                ->setEntityId($idString)
                ->generateUrl();

            return sprintf('<a href="%s">%s</a>', $url, htmlspecialchars($idString));
        })
        ->renderAsHtml()
            ->onlyOnIndex();
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
        yield ImageField::new('image', $this->translateService->translateSzavak("image"))
            ->setBasePath('/uploads/slide')
            ->formatValue(function ($value, $entity) {
                if (!$value) {
                    return null;
                }

                return "/uploads/slide/{$value}.webp";
            })
            ->addCssClass('index-image')
            ->onlyOnIndex();
        yield DateField::new('created_at', $this->translateService->translateSzavak("created_at","created"))->hideOnForm();
        yield DateField::new('modified_at',$this->translateService->translateSzavak("modified_at","modified"))->hideOnForm();
        yield BooleanField::new('active', $this->translateService->translateSzavak("active"))
            ->renderAsSwitch(true)
            ->onlyOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->addFormTheme('admin/slide/slide_upload_with_preview.html.twig')
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig')
            ->overrideTemplates([
                'crud/index' => 'admin/slide/index.html.twig',
            ]);
    }
}
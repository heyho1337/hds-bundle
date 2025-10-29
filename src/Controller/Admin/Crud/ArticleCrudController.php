<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Article;
use App\Service\Admin\CrudService;
use App\Service\Modules\ImageService;
use App\Service\Modules\LangService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;

class ArticleCrudController extends AbstractCrudController
{   
    private string $lang;
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private ImageService $imageService,
        private readonly CrudService $crudService,
        private readonly LangService $langService,
        private readonly TranslateService $translateService,
        private readonly RequestStack $requestStack,
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
        return Article::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;

        $file = $this->getContext()->getRequest()->files->get('Article')['image'] ?? null;
        $this->imageService->processImage($file, $entityInstance,"article");

        $this->crudService->setEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;

        /** @var UploadedFile|null $file */
        $file = $this->getContext()->getRequest()->files->get('Article')['image'] ?? null;
        $this->imageService->processImage($file, $entityInstance,"article");

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


        yield FormField::addTab($this->translateService->translateSzavak($this->langService->getDefaultObject()->getLangsName()));
            yield TextField::new('name_'.$this->langService->getDefault(), $this->translateService->translateSzavak("name"))
                ->hideOnIndex();
            yield TextField::new('title_'.$this->langService->getDefault(), $this->translateService->translateSzavak("title"))->hideOnIndex();
            yield Field::new('text_'.$this->langService->getDefault(), $this->translateService->translateSzavak("text"))
                ->setFormType(CKEditorType::class)
                ->onlyOnForms();
            yield TextField::new('meta_desc_'.$this->langService->getDefault(), $this->translateService->translateSzavak("meta_desc","meta desc"))->hideOnIndex();
        
        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isLangsDefault()){
                yield FormField::addTab($this->translateService->translateSzavak($lang->getLangsName()));
                yield TextField::new('name_'.$lang->getLangsCode(), $this->translateService->translateSzavak("name"))
                    ->hideOnIndex();
                yield TextField::new('title_'.$lang->getLangsCode(), $this->translateService->translateSzavak("title"))->hideOnIndex();
                yield Field::new('text_'.$lang->getLangsCode(), $this->translateService->translateSzavak("text"))
                    ->setFormType(CKEditorType::class)
                    ->onlyOnForms();
                yield TextField::new('meta_desc_'.$lang->getLangsCode(), $this->translateService->translateSzavak("meta_desc","meta desc"))->hideOnIndex();
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
        yield DateField::new('created_at', $this->translateService->translateSzavak("created_at", "created"))->hideOnForm();
        yield DateField::new('modified_at',$this->translateService->translateSzavak("modified_at", "modified"))->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->addFormTheme('admin/article/article_upload_with_preview.html.twig')
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig');
    }
}

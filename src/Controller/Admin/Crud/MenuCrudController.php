<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Article;
use App\Entity\Menu;
use App\Service\Admin\CrudService;
use App\Service\Modules\LangService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

class MenuCrudController extends AbstractCrudController
{
    
    private string $lang;

    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly CrudService $crudService,
        private readonly LangService $langService,
        private readonly TranslateService $translateService,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $entityManager
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
        return Menu::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Menu) return;

        $this->crudService->setEntity($entityManager, $entityInstance);
        $this->setArticle($entityInstance,$entityManager);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Menu) return;

        $this->crudService->setEntity($entityManager, $entityInstance);
        $this->setArticle($entityInstance,$entityManager);
    }

    private function setArticle(object $entityInstance,EntityManagerInterface $entityManager): void
    {
        $type = $entityInstance->getType();
        if($type){
            if($entityInstance->getType()->getId() === 1 && !$entityInstance->getArticle()){
                $article = new Article();
                foreach($this->langService->getLangs() as $lang){
                    $setNameMethod = "setName".ucfirst($lang->getLangsCode());
                    $getNameMethod = "getName".ucfirst($lang->getLangsCode());

                    $setTitleMethod = "setTitle".ucfirst($lang->getLangsCode());
                    $setMetaMethod = "setMetaDesc".ucfirst($lang->getLangsCode());

                    $article->$setNameMethod($entityInstance->$getNameMethod());
                    $article->$setTitleMethod($entityInstance->$getNameMethod());
                    $article->$setMetaMethod($entityInstance->$getNameMethod());
                }
                
                $this->crudService->setEntity($entityManager, $article);

                $entityInstance->setArticle($article);
                $entityManager->persist($entityInstance);
                $entityManager->flush();
            }
        }
    }

    public function configureFields(string $pageName): iterable
    {
        Menu::setCurrentLang($this->lang);
        $this->getContext()->getRequest()->setLocale($this->lang);
        $this->translator->getCatalogue($this->lang);
        $this->translator->setLocale($this->lang);

        /**
         * on forms
         */
        
        yield FormField::addTab($this->translateService->translateSzavak("options"));
            yield BooleanField::new('active',$this->translateService->translateSzavak("active"))
                ->renderAsSwitch(true)
                ->setFormTypeOptions(['data' => true])
                ->onlyOnForms();
            yield AssociationField::new('position', $this->translateService->translateSzavak("position"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();
            yield AssociationField::new('target', $this->translateService->translateSzavak("target"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();
            yield AssociationField::new('parent', $this->translateService->translateSzavak("parent", "parent menu"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();
            yield AssociationField::new('type', $this->translateService->translateSzavak("type"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('attr', [
                    'data-menu-type-target' => 'typeField',
                    'data-action' => 'change->menu-type#changeType'
                ])
                ->hideOnIndex();
            yield AssociationField::new('article', $this->translateService->translateSzavak("article"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'articleRow'])
                ->hideOnIndex();
            yield AssociationField::new('blog', $this->translateService->translateSzavak("blog"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'blogRow'])
                ->hideOnIndex();
            yield AssociationField::new('blog_category', $this->translateService->translateSzavak("blog_category", 'blog category'))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'blogCategoryRow'])
                ->hideOnIndex();
            yield AssociationField::new('tag', $this->translateService->translateSzavak("tag"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'tagRow'])
                ->hideOnIndex();
            yield Field::new('file', $this->translateService->translateSzavak("file"))
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['data-menu-type-target' => 'fileRow']
                ])
                ->onlyOnForms();
            
        
        yield FormField::addTab($this->translateService->translateSzavak($this->langService->getDefaultObject()->getLangsName()));
            yield TextField::new('name_'.$this->langService->getDefault(), $this->translateService->translateSzavak("name"))
                ->hideOnIndex();
            yield TextField::new('slug_'.$this->langService->getDefault(), $this->translateService->translateSzavak("url"))
                ->hideOnIndex();

        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isLangsDefault()){
                yield FormField::addTab($this->translateService->translateSzavak($lang->getLangsName()));
                yield TextField::new('name_'.$lang->getLangsCode(), $this->translateService->translateSzavak("name"))
                    ->hideOnIndex();
                yield TextField::new('slug_'.$lang->getLangsCode(), $this->translateService->translateSzavak("url"))
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
        yield TextField::new('slug_'.$this->langService->getDefault(), $this->translateService->translateSzavak("url"))->onlyOnIndex();
        yield DateField::new('created_at', $this->translateService->translateSzavak("created_at", "created"))->hideOnForm();
        yield DateField::new('modified_at',$this->translateService->translateSzavak("modified_at", "modified"))->hideOnForm();
        yield BooleanField::new('active', $this->translateService->translateSzavak("active"))
            ->renderAsSwitch(true)
            ->onlyOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig')
            ->overrideTemplates([
                'crud/index' => 'admin/menu/index.html.twig',
            ])
            ->setDefaultSort(['order_num' => 'ASC'])
            ->addFormTheme('admin/crud/menu_crud_form_theme.html.twig');
    }

    public function createAutocompleteQueryBuilder(string $searchQuery, array $criteria, string $entityAlias, string $searchField): QueryBuilder
    {
        // For 'parent' field, load all Menu entities (ignoring $searchQuery)
        if ($searchField === 'parent') {
            $qb = $this->getDoctrine()->getRepository(Menu::class)->createQueryBuilder($entityAlias);
            $qb->orderBy("$entityAlias.name_$this->lang", 'ASC');
            return $qb;
        }

        // Default autocomplete behavior for other fields
        $qb = parent::createAutocompleteQueryBuilder($searchQuery, $criteria, $entityAlias, $searchField);
        $qb->select("$entityAlias.id, $entityAlias.name_$this->lang AS label");

        return $qb;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->entityManager->getRepository(Menu::class)
        ->createQueryBuilder('m')
        ->orderBy('m.position', 'ASC')
        ->addOrderBy('m.order_num', 'ASC');

        return $qb;
    }
}

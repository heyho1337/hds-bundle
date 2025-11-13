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
                    $code = $lang->getCode();
                    
                    // ✅ Use getter/setter with language parameter instead of dynamic methods
                    $name = $entityInstance->getName($code);
                    $article->setName($name, $code);
                    $article->setTitle($name, $code);
                    $article->setMetaDesc($name, $code);
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
        $this->getContext()->getRequest()->setLocale($this->lang);
        $this->translator->getCatalogue($this->lang);
        $this->translator->setLocale($this->lang);

        /**
         * on forms
         */
        
        yield FormField::addTab($this->translateService->translateWords("options"));
            yield BooleanField::new('active',$this->translateService->translateWords("active"))
                ->renderAsSwitch(true)
                ->setFormTypeOptions(['data' => true])
                ->onlyOnForms();
            yield AssociationField::new('position', $this->translateService->translateWords("position"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();
            yield AssociationField::new('target', $this->translateService->translateWords("target"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();
            yield AssociationField::new('parent', $this->translateService->translateWords("parent", "parent menu"))
                ->setRequired(false)
                ->autocomplete()
                ->hideOnIndex();
            yield AssociationField::new('type', $this->translateService->translateWords("type"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('attr', [
                    'data-menu-type-target' => 'typeField',
                    'data-action' => 'change->menu-type#changeType'
                ])
                ->hideOnIndex();
            yield AssociationField::new('article', $this->translateService->translateWords("article"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'articleRow'])
                ->hideOnIndex();
            yield AssociationField::new('blog', $this->translateService->translateWords("blog"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'blogRow'])
                ->hideOnIndex();
            yield AssociationField::new('blog_category', $this->translateService->translateWords("blog_category", 'blog category'))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'blogCategoryRow'])
                ->hideOnIndex();
            yield AssociationField::new('tag', $this->translateService->translateWords("tag"))
                ->setRequired(false)
                ->autocomplete()
                ->setFormTypeOption('row_attr', ['data-menu-type-target' => 'tagRow'])
                ->hideOnIndex();
            yield Field::new('file', $this->translateService->translateWords("file"))
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['data-menu-type-target' => 'fileRow']
                ])
                ->onlyOnForms();
        
        // ✅ Default language tab - use custom getter/setter
        yield FormField::addTab($this->translateService->translateWords($this->langService->getDefaultObject()->getName()));
            yield TextField::new('name', $this->translateService->translateWords("name"))
                ->setFormTypeOption('getter', function(Menu $entity) {
                    return $entity->getName($this->langService->getDefault());
                })
                ->setFormTypeOption('setter', function(Menu &$entity, $value) {
                    $entity->setName($value, $this->langService->getDefault());
                })
                ->hideOnIndex();
            yield TextField::new('slug', $this->translateService->translateWords("url"))
                ->setFormTypeOption('getter', function(Menu $entity) {
                    return $entity->getSlug($this->langService->getDefault());
                })
                ->setFormTypeOption('setter', function(Menu &$entity, $value) {
                    $entity->setSlug($value, $this->langService->getDefault());
                })
                ->hideOnIndex();

        // ✅ Other language tabs - use custom getter/setter for each
        foreach($this->langService->getLangs() as $lang){
            if(!$lang->isDefault()){
                $langCode = $lang->getCode();
                
                yield FormField::addTab($this->translateService->translateWords($lang->getName()));
                
                yield TextField::new('name_' . $langCode, $this->translateService->translateWords("name"))
                    ->setFormTypeOption('getter', function(Menu $entity) use ($langCode) {
                        return $entity->getName($langCode);
                    })
                    ->setFormTypeOption('setter', function(Menu &$entity, $value) use ($langCode) {
                        $entity->setName($value, $langCode);
                    })
                    ->hideOnIndex();
                
                yield TextField::new('slug_' . $langCode, $this->translateService->translateWords("url"))
                    ->setFormTypeOption('getter', function(Menu $entity) use ($langCode) {
                        return $entity->getSlug($langCode);
                    })
                    ->setFormTypeOption('setter', function(Menu &$entity, $value) use ($langCode) {
                        $entity->setSlug($value, $langCode);
                    })
                    ->hideOnIndex();
            }
        }
        
        /**
         * index
         */
        yield TextField::new('name', $this->translateService->translateWords("name"))
            ->formatValue(function ($value, Menu $entity) {
                $default = $this->langService->getDefault();
                $name = $entity->getName($default);
                
                $url = $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('edit')
                    ->setEntityId($entity->getId())
                    ->generateUrl();

                return sprintf('<a href="%s">%s</a>', $url, htmlspecialchars($name));
            })
            ->onlyOnIndex()
            ->renderAsHtml();
        
        yield TextField::new('slug', $this->translateService->translateWords("url"))
            ->formatValue(function ($value, Menu $entity) {
                return $entity->getSlug($this->langService->getDefault());
            })
            ->onlyOnIndex();
        
        yield DateField::new('created_at', $this->translateService->translateWords("created_at", "created"))->hideOnForm();
        yield DateField::new('modified_at',$this->translateService->translateWords("modified_at", "modified"))->hideOnForm();
        yield BooleanField::new('active', $this->translateService->translateWords("active"))
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
            $qb->orderBy("$entityAlias.order_num", 'ASC');
            return $qb;
        }

        // ✅ Use JSON_EXTRACT for autocomplete
        $qb = parent::createAutocompleteQueryBuilder($searchQuery, $criteria, $entityAlias, $searchField);
        $qb->select("$entityAlias.id, JSON_UNQUOTE(JSON_EXTRACT($entityAlias.name, '$.\"{$this->lang}\"')) AS label");

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

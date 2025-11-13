<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Accordion;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Menu;
use App\Entity\Schema;
use App\Entity\Article;
use App\Entity\MenuPosition;
use App\Entity\Config;
use App\Entity\Gallery;
use App\Entity\MenuTarget;
use App\Entity\MenuType;
use App\Entity\User;
use App\Entity\Slide;
use App\Entity\Form;
use App\Entity\FormType;
use App\Repository\ComponentRepository;
use App\Repository\ConfigRepository;
use App\Service\Modules\LangService;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private array $langs;
    private Config $config;
    private array $component;
    private string $lang;

    public function __construct(
        private readonly LangService $langService,
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly TranslateService $translateService,
        private readonly Security $security,
        private readonly ConfigRepository $configRepo,
        private readonly ComponentRepository $compRepo,

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
    
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin');
    }

    private function hasAccess(array|null $roles): bool
    {
        if(is_array($roles)){
            foreach($roles as $role) {
                if($this->security->isGranted($role)) {
                    return true;
                }
            }
        }
        else{
            return true;
        }
        return false;
    }

    public function configureMenuItems(): iterable
    {
        $this->config = $this->configRepo->findOneBy(['id' => 1]);
        $components = $this->compRepo->findAll();
        foreach ($components as $component) {
            $this->component[$component->getGroupName()][] = $component;
        }

        foreach($this->component as $group => $menuItems){
            yield MenuItem::section($this->translateService->translateWords($group));
            if($group === 'basic'){
                yield MenuItem::linkToDashboard($this->translateService->translateWords("dashboard"), 'fa fa-home');
            }
            foreach ($menuItems as $menu) {
                $entityClass = 'App\\Entity\\' . $menu->getClass();
                if($this->hasAccess($menu->getRole())){
                    if($menu->getName() === 'config'){
                        yield MenuItem::linkToCrud(
                            $this->translateService->translateWords($menu->getName(),$menu->getLabel()), 
                            $menu->getIcon(), 
                            $entityClass
                        )
                        ->setAction('edit')
                        ->setEntityId(1);
                    }
                    else{
                        yield MenuItem::linkToCrud(
                            $this->translateService->translateWords($menu->getName(),$menu->getLabel()), 
                            $menu->getIcon(), 
                            $entityClass
                        );
                    }
                }
            }
        } 
        
        if ($this->security->isGranted('ROLE_SUPER_ADMIN') && $this->config->isMultilang()) {   
            yield MenuItem::section($this->translateService->translateWords("languages"));
                $request = $this->requestStack->getCurrentRequest();
                $routeName = $request->attributes->get('_route');
                $routeParams = $request->attributes->get('_route_params', []);

                // Save current context default locale
                $currentDefaultLocale = $this->router->getContext()->getParameter('_locale');

                // Temporarily set to null or different locale to force adding _locale param
                $this->router->getContext()->setParameter('_locale', null);
                
                foreach ($this->langs as $locale) {
                    $params = array_merge($routeParams, ['_locale' => $locale->getCode()]);
                    $url = $this->router->generate($routeName, $params);

                    // If default locale and _locale is missing, append manually
                    if ($locale->getCode() === $this->langService->getDefault() && strpos($url, '_locale=') === false) {
                        $url .= (strpos($url, '?') === false ? '?' : '&') . '_locale='.$this->langService->getDefault();
                    }

                    yield MenuItem::linkToUrl(
                        $this->translateService->translateWords($locale->getCode()."_desc",$locale->getName()),
                        'fa fa-globe',
                        $url
                    );
                }

            $this->router->getContext()->setParameter('_locale', $currentDefaultLocale);
        }
    }

}

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
use App\Service\Modules\LangService;
use App\Service\Modules\TranslateService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private array $langs;
    public function __construct(
        private readonly LangService $langService,
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly TranslateService $translateService,
        private readonly Security $security

    ) {
        $this->langs = $this->langService->getLangs();
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

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section($this->translateService->translateSzavak("basic"));
            yield MenuItem::linkToDashboard($this->translateService->translateSzavak("dashboard"), 'fa fa-home');
            if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
                yield MenuItem::linkToCrud($this->translateService->translateSzavak("schema"), 'fa fa-tags', Schema::class);
                yield MenuItem::linkToCrud($this->translateService->translateSzavak("config"), 'fa fa-tags', Config::class)
                    ->setAction('edit')
                    ->setEntityId(1);
                yield MenuItem::linkToCrud($this->translateService->translateSzavak("user"), 'fa fa-tags', User::class);
            }

        yield MenuItem::section($this->translateService->translateSzavak("menu"));
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("menu","menu type"), 'fa fa-tags', Menu::class);
            if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
                yield MenuItem::linkToCrud($this->translateService->translateSzavak("menu_pos","menu positions"), 'fa fa-tags', MenuPosition::class);
                yield MenuItem::linkToCrud($this->translateService->translateSzavak("menu_target","menu target"), 'fa fa-tags', MenuTarget::class);
                yield MenuItem::linkToCrud($this->translateService->translateSzavak("menu_type","menu type"), 'fa fa-tags', MenuType::class);
            }
        
        yield MenuItem::section($this->translateService->translateSzavak("blog"));
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("categories"), 'fa fa-tags', Category::class);
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("blog_posts","Blog Posts"), 'fa fa-file-text', Blog::class);
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("tags"), 'fa fa-file-text', Tag::class);

        yield MenuItem::section($this->translateService->translateSzavak("content"));
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("articles"), 'fa fa-file-text', Article::class);
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("accordion"), 'fa fa-file-text', Accordion::class);

        yield MenuItem::section($this->translateService->translateSzavak("images"));
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("slide"), 'fa fa-panorama', Slide::class);
            yield MenuItem::linkToCrud($this->translateService->translateSzavak("gallery"), 'fa fa-panorama', Gallery::class);
        
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {   
            yield MenuItem::section($this->translateService->translateSzavak("languages"));
                $request = $this->requestStack->getCurrentRequest();
                $routeName = $request->attributes->get('_route');
                $routeParams = $request->attributes->get('_route_params', []);

                // Save current context default locale
                $currentDefaultLocale = $this->router->getContext()->getParameter('_locale');

                // Temporarily set to null or different locale to force adding _locale param
                $this->router->getContext()->setParameter('_locale', null);
                
                foreach ($this->langs as $locale) {
                    $params = array_merge($routeParams, ['_locale' => $locale->getLangsCode()]);
                    $url = $this->router->generate($routeName, $params);

                    // If default locale and _locale is missing, append manually
                    if ($locale->getLangsCode() === $this->langService->getDefault() && strpos($url, '_locale=') === false) {
                        $url .= (strpos($url, '?') === false ? '?' : '&') . '_locale='.$this->langService->getDefault();
                    }

                    yield MenuItem::linkToUrl(
                        $this->translateService->translateSzavak($locale->getLangsCode()."_desc",$locale->getLangsName()),
                        'fa fa-globe',
                        $url
                    );
                }

            $this->router->getContext()->setParameter('_locale', $currentDefaultLocale);
        }
    }

}

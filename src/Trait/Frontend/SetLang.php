<?php

namespace App\Trait\Frontend;

use App\Service\Modules\LangService;
use App\Service\Modules\TranslateService;
use App\Entity\Langs;
use Symfony\Component\HttpFoundation\RequestStack;

Trait SetLang
{
    private function setLang(
        LangService $langService,
        TranslateService $translateService,
        RequestStack $requestStack,
    ): string
    {
        $lang = $langService->getDefault();
        if($requestStack->getCurrentRequest()){
            $locale = $requestStack->getCurrentRequest()->getSession()->get('_locale');
            if($locale){
                $lang = $requestStack->getCurrentRequest()->getSession()->get('_locale');
                $translateService->setLangs($lang);
                $langService->setLang($lang);
            }
        }

        return $lang;
    } 
}
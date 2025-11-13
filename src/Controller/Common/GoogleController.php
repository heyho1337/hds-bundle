<?php

namespace App\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\Request;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google_main')
            ->redirect([
                'openid', 'https://www.googleapis.com/auth/userinfo.email'
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): Response
    {
        $client = $clientRegistry->getClient('google_main');
        
        try {
            $user = $client->fetchUser();
            
            // Do something with the user data
            // For example, authenticate them, create/update user record, etc.
            
            // Then redirect to a success page
            return $this->redirectToRoute('configurator_home'); // or wherever you want
            
        } catch (IdentityProviderException $e) {
            // Handle the error properly and return a response
            $this->addFlash('error', 'Google authentication failed: ' . $e->getMessage());
            return $this->redirectToRoute('admin_login'); // or error page
        }
    }

}
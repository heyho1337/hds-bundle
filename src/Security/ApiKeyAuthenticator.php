<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('x-api-key');
    }

    public function authenticate(Request $request): Passport
    {
        $clientApiKey = $request->headers->get('x-api-key');

        if (!$clientApiKey || $clientApiKey !== $this->apiKey) {
            throw new CustomUserMessageAuthenticationException('Invalid API Key.');
        }

        return new SelfValidatingPassport(new UserBadge('api_user', function ($userIdentifier) {
            // Return a user instance that carries the ROLE_API role
            return new ApiKeyUser();
        }));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentication Failed: ' . $exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null; // Continue processing the request normally
    }
}

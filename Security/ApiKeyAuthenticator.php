<?php

namespace Manatee\CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    protected $provider;

    public function __construct(ApiKeyUserProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Early in the request cycle, Symfony calls createToken(). Your job here is to create a token object that
     * contains all of the information from the request that you need to authenticate the user (e.g. the apikey query
     * parameter). If that information is missing, throwing a BadCredentialsException will cause authentication to fail.
     * You might want to return null instead to just skip the authentication, so Symfony can fallback to another
     * authentication method, if any.
     *
     * @param Request $request
     * @param $providerKey
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        // Parse request
        $apiKey = $request->headers->get('x-authorization');

        if (!$apiKey) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    /**
     * If supportsToken() returns true, Symfony will now call authenticateToken(). One key part is the $userProvider,
     * which is an external class that helps you load information about the user.
     *
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        // Get apiKey from Token
        $apiKey = $token->getCredentials();

        // Load username from apiKey
        $username = $this->provider->getUsernameForApiKey($apiKey);

        if (!$username) {
            throw new AuthenticationException(
                sprintf('API Key "%s" does not exist.', $apiKey)
            );
        }

        $user = $this->provider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed.", 403);
    }

    /**
     * After Symfony calls createToken(), it will then call supportsToken() on your class (and any other authentication
     * listeners) to figure out who should handle the token. This is just a way to allow several authentication
     * mechanisms to be used for the same firewall (that way, you can for instance first try to authenticate the user
     * via a certificate or an API key and fall back to a form login).
     *
     * Mostly, you just need to make sure that this method returns true for a token that has been created by
     * createToken(). Your logic should probably look exactly like this example.
     *
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
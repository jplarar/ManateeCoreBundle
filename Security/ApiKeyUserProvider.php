<?php

namespace Manatee\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * For database management
     *
     * @var Doctrine $doctrine
     */
    private $doctrine;

    /**
     * Use service with Doctrine as argument.
     *
     * @param Doctrine $doctrine
     */
    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get username from the API key
     * @param $apiKey
     * @return string
     */
    public function getUsernameForApiKey($apiKey)
    {
        /** @var \Manatee\CoreBundle\Entity\SessionLog $session */
        $session = $this->doctrine->getRepository("ManateeCoreBundle:SessionLog")->findOneBy(array(
            'apiKey' => $apiKey
        ));

        if (!$session) {
            throw new AuthenticationException(
                sprintf('Session not found in database.')
            );
        }

        return $session->getUserId()->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        /** @var \Manatee\CoreBundle\Entity\User $user */
        $user = $this->doctrine->getRepository("ManateeCoreBundle:User")->findOneBy(array(
            'username' => $username
        ));

        if (!$user) {
            throw new AuthenticationException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'Manatee\CoreBundle\Entity\User' === $class;
    }
}
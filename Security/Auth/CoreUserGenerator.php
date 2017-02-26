<?php

namespace Limit0\ModlrAuthBundle\Security\Auth;

use Limit0\ModlrAuthBundle\Security\JWT\JWTGeneratorManager;
use Limit0\ModlrAuthBundle\Security\User\CoreUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Generates auth data for a core user.
 *
 * @author Jacob Bare <jacob@limit0io>
 */
class CoreUserGenerator implements AuthGeneratorInterface
{
    /**
     * @var JWTGeneratorManager
     */
    private $jwtManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param   JWTGeneratorManager     $jwtManager
     * @param   RequestStack            $requestStack
     */
    public function __construct(JWTGeneratorManager $jwtManager, RequestStack $requestStack)
    {
        $this->jwtManager   = $jwtManager;
        $this->requestStack = $requestStack;
    }

    /**
     *{@inheritdoc}
     */
    public function generateFor(UserInterface $user)
    {
        $data = [
            'id'            => $user->getIdentifier(),
            'username'      => $user->getUserName(),
            'givenName'     => $user->getGivenName(),
            'familyName'    => $user->getFamilyName(),
            'roles'         => $user->getRoles(),
            'token'         => $this->jwtManager->createFor($user),
        ];
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(UserInterface $user)
    {
        return $user instanceof CoreUser;
    }
}

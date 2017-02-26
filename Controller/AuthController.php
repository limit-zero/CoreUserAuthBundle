<?php

namespace Limit0\ModlrAuthBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthController extends Controller
{
    /**
     * Creates a new, core user.
     *
     */
    public function userCreateAction()
    {
        // @todo Implement. Must ensure the user has appropriate permission to create new users.
        throw new \BadMethodCallException('NYI');
    }

    /**
     * Retrieves the core user auth state.
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function userRetrieveAction(Request $request)
    {
        $token   = $this->getUserToken();
        $manager = $this->get('modlr_auth_bundle.security.auth.generator_manager');
        return $manager->createResponseFor($token->getUser());
    }

    /**
     * Gets the current security user token.
     *
     * @return  \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    protected function getUserToken()
    {
        return $this->get('security.token_storage')->getToken();
    }
}

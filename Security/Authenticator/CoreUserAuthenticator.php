<?php

namespace Limit0\ModlrAuthBundle\Security\Authenticator;

use Limit0\ModlrAuthBundle\Exception\HttpFriendlyException;
use Limit0\ModlrAuthBundle\Security\Auth\AuthGeneratorManager;
use As3\Modlr\Api\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\HttpUtils;

class CoreUserAuthenticator extends AbstractCoreAuthenticator
{
    const USERNAME = 'username';
    const PASSWORD = 'password';

    /**
     * @var AuthGeneratorManager
     */
    private $authManager;

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    /**
     * @param   AdapterInterface        $adapter
     * @param   HttpUtils               $httpUtils
     * @param   EncoderFactory          $encoderFactory
     * @param   AuthGeneratorManager    $authManager
     */
    public function __construct(AdapterInterface $adapter, HttpUtils $httpUtils, EncoderFactory $encoderFactory, AuthGeneratorManager $authManager)
    {
        parent::__construct($adapter, $httpUtils);
        $this->authManager    = $authManager;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $passField = self::PASSWORD;
        $userField = self::USERNAME;
        if (empty($credentials[$userField]) || empty($credentials[$passField])) {
            throw new BadCredentialsException('The presented credentials cannot be empty.');
        }

        return $this->encoderFactory->getEncoder($user)->isPasswordValid(
            $user->getPassword(),
            $credentials[$passField],
            $user->getSalt()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials[self::USERNAME]);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->authManager->createResponseFor($token->getUser());
    }

    /**
     * {@inheritdoc}
     */
    protected function extractCredentials(Request $request)
    {
        return $this->extractPayload($request);
    }

    private function extractPayload(Request $request, $flat = true)
    {
        if ('GET' === $request->getMethod()) {
            $payload = [];
            $payload['data'] = $request->query->all();
            return (true == $flat) ? $payload['data'] : $payload;
        }

        if (0 !== stripos($request->headers->get('content-type'), 'application/json')) {
            throw new HttpFriendlyException('Invalid request content type. Expected application/json.', 415);
        }
        // JSON request.
        $payload = @json_decode($request->getContent(), true);
        if (!isset($payload['data'])) {
            throw new HttpFriendlyException('No data member was found in the request payload.', 422);
        }

        $payload['data'] = (array) $payload['data'];

        return (true == $flat) ? $payload['data'] : $payload;
    }
}

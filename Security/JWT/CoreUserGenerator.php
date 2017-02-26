<?php

namespace Limit0\ModlrAuthBundle\Security\JWT;

use Limit0\ModlrAuthBundle\Security\User\CoreUser;
use Lcobucci\JWT\Builder as JWTBuilder;
use Lcobucci\JWT\Parser as JWTParser;
use Lcobucci\JWT\Signer\Hmac\Sha256 as JWTSigner;
use Lcobucci\JWT\ValidationData;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Generates (and parses) JWT tokens for core users.
 *
 * @author  Jacob Bare <jacob.bare@gmail.com>
 */
class CoreUserGenerator implements JWTGeneratorInterface
{
    /**
     * @var string
     */
    private $issuer = 'modlr-auth';

    /**
     * @var int
     */
    private $ttl = 86400;

    /**
     * @var JWTBuilder
     */
    private $builder;

    /**
     * @var JWTParser
     */
    private $parser;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var JWTSigner
     */
    private $signer;

    /**
     * @param   string  $secret
     */
    public function __construct($secret, $issuer = 'modlr-auth', $ttl = 86400)
    {
        if (empty($secret)) {
            throw new \InvalidArgumentException('The token secret cannot be empty.');
        }

        $this->issuer = $issuer;
        $this->ttl = $ttl;

        $this->builder = new JWTBuilder();
        $this->parser  = new JWTParser();
        $this->secret  = $secret;
        $this->signer  = new JWTSigner();
    }

    /**
     * {@inheritdoc}
     */
    public function createFor(UserInterface $user)
    {
        $now     = time();
        $expires = $now + $this->ttl;

        $jwt = $this->builder
            ->setSubject($user->getUsername())
            ->setIssuer($this->issuer)
            ->setExpiration($expires)
            ->setIssuedAt($now)
            ->setAudience($this->getAudienceKey())
            ->sign($this->signer, $this->secret)
            ->getToken()
        ;
        return (string) $jwt;
    }

    /**
     * {@inheritdoc}
     */
    public function getAudienceKey()
    {
        return 'core-user';
    }

    /**
     * {@inheritdoc}
     */
    public function parseFor($token)
    {
        $token = $this->parser->parse($token);

        $rules = new ValidationData();
        $rules->setIssuer($this->issuer);
        $rules->setAudience($this->getAudienceKey());

        if (false === $token->validate($rules)) {
            throw new AuthenticationException('Invalid token.');
        }
        if (false === $token->verify($this->signer, $this->secret)) {
            throw new AuthenticationException('Invalid token.');
        }
        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(UserInterface $user)
    {
        return $user instanceof CoreUser;
    }
}

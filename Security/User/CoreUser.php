<?php

namespace Limit0\ModlrAuthBundle\Security\User;

use \Serializable;
use Limit0\ModlrAuthBundle\Core\AccountManager;
use Limit0\ModlrAuthBundle\Cors\CorsDefinition as Cors;
use As3\Modlr\Models\Model;
use Symfony\Component\Security\Core\User\UserInterface;

class CoreUser implements UserInterface, Serializable
{
    private $applications = [];

    private $identfier;

    private $model;

    private $origin;

    private $familyName;

    private $givenName;

    private $password;

    private $publicKeys = [];

    private $roles = [];

    private $salt;

    private $username;

    public function __construct(Model $model)
    {
        $this->model      = $model;
        $this->identifier = $model->getId();
        $this->familyName = $model->get('familyName');
        $this->givenName  = $model->get('givenName');
        $this->password   = $model->get('password');
        $this->salt       = $model->get('salt');
        $this->username   = $model->get('email');

        $roles = $model->get('roles');
        $this->roles = is_array($roles) && !empty($roles) ? $roles : ['ROLE_ADMIN\USER'];
    }

    /**
     * @return  string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @return  string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Gets the user database id.
     *
     * @return  string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Gets the user model for this user instance.
     *
     * @return  Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->identifier,
            $this->familyName,
            $this->givenName,
            $this->password,
            $this->roles,
            $this->salt,
            $this->username,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->identifier,
            $this->familyName,
            $this->givenName,
            $this->password,
            $this->roles,
            $this->salt,
            $this->username
        ) = unserialize($serialized);
    }
}

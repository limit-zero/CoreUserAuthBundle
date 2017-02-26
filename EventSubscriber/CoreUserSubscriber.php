<?php

namespace Limit0\ModlrAuthBundle\EventSubscriber;

use As3\Modlr\Models\Model;
use As3\Modlr\Events\EventSubscriberInterface;
use As3\Modlr\Store\Events;
use As3\Modlr\Store\Events\ModelLifecycleArguments;

use Limit0\ModlrAuthBundle\Security\User\CoreUser;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Handles horse-profile models on commit to Modlr.
 *
 * @author Jacob Bare <jacob@limit0.io>
 */
class CoreUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoder
     */
    protected $encoder;

    /**
     * DI
     *
     * @param   UserPasswordEncoder     $encoder
     */
    public function __construct(UserPasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritDoc}
     */
    public function getEvents()
    {
        return [
            Events::preCommit,
        ];
    }

    /**
     * Processes event models on any commit (create, update, or delete)
     *
     * @param   ModelLifecycleArguments     $args
     */
    public function preCommit(ModelLifecycleArguments $args)
    {
        $model = $args->getModel();
        if (false === $this->shouldProcess($model)) {
            return;
        }

        $this->formatEmailAddress($model);

        if (isset($model->getChangeSet()['attributes']['password'])) {
            $password = $model->getChangeSet()['attributes']['password'];

            // If the password was nulled out in the interface, ignore this.
            if (null === $password['new']) {
                $model->set('password', $password['old']);
                return;
            }

            $password = $model->get('password');
            if (null !== $password && 0 === preg_match('/^\$2[ayb]\$.{56}$/i', $password)) {
                // The password is currently clear text. Encode.
                $encoded = $this->encoder->encodePassword(new CoreUser($model), $password);
                $model->set('password', $encoded);
            }
        }

        if (empty($model->get('password'))) {
            throw new \InvalidArgumentException('All users must be assigned a password.');
        }
    }

    /**
     * Determines if this subscriber should handle the model.
     * Must be an event model.
     *
     * @param   Model   $model
     * @return  bool
     */
    protected function shouldProcess(Model $model)
    {
        return 'core-user' === $model->getType();
    }

    /**
     * @param   Model   $model
     * @throws  \InvalidArgumentException
     */
    private function formatEmailAddress(Model $model)
    {
        $value = $model->get('email');
        $value = trim($value);
        if (empty($value)) {
            throw new \InvalidArgumentException('The user email value cannot be empty.');
        }
        $value = strtolower($value);
        if (false === stripos($value, '@')) {
            throw new \InvalidArgumentException(sprintf('The provided email address "%s" is invalid.', $value));
        }
        $model->set('email', $value);
    }
}

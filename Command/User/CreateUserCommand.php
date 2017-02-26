<?php

namespace Limit0\ModlrAuthBundle\Command\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates a user account
 *
 * @author  Josh Worden <josh@limit0.io>
 */
class CreateUserCommand extends Command
{
    /**
     * @var Store
     */
    private $store;

    /**
     * Constructor.
     *
     * @param   Store     $cacheWarmer
     */
    public function __construct(Store $store)
    {
        parent::__construct();
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('limit0:auth:create:user')
            ->setDescription('Creates a user.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating a new user.</info>');

        $kvs = [
            'username'  => $input->ask('Username: '),
            'password'  => $input->ask('Password: '),
            'email'     => $input->ask('Email:'),
            'givenName' => $input->ask('Given Name: '),
            'familyName'=> $input->ask('Family Name: '),
        ];


        $user = $this->store->create('core-user', $kvs);

        $output->writeln(sprintf('<info>Created user "%s"</info>', $user->getId()));
    }
}

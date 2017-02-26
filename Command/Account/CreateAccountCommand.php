<?php

namespace Limit0\ModlrAuthBundle\Command\Account;

use As3\Modlr\Store\Store;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates a user account
 *
 * @author  Josh Worden <josh@limit0.io>
 */
class CreateAccountCommand extends Command
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
            ->setName('limit0:auth:create:account')
            ->setDescription('Creates an account.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating a new account.</info>');

        $kvs = [
            'name'  => $input->ask('Name: '),
            'key'   => $input->ask('Key: '),
        ];

        $user = $this->store->create('core-account', $kvs);
        $output->writeln(sprintf('<info>Created account "%s"</info>', $user->getId()));
    }
}

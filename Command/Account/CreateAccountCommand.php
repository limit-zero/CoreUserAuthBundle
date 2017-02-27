<?php

namespace Limit0\ModlrAuthBundle\Command\Account;

use As3\Modlr\Store\Store;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $helper = $this->getHelper('question');

        $kvs = [
            'name'  => $helper->ask($input, $output, new Question('Account Name: ')),
            'key'   => $helper->ask($input, $output, new Question('Account Key: ')),
        ];

        $account = $this->store->create('core-account');
        foreach ($kvs as $k => $v) {
            $account->set($k, $v);
        }
        $account->save();

        $output->writeln(sprintf('<info>Created account "%s"</info>', $account->getId()));
    }
}

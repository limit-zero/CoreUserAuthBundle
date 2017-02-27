<?php

namespace Limit0\ModlrAuthBundle\Command\User;

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
        $helper = $this->getHelper('question');

        $kvs = [
            'username'  => $helper->ask($input, $output, new Question('Username: ')),
            'password'  => $helper->ask($input, $output, new Question('Password: ')),
            'email'     => $helper->ask($input, $output, new Question('Email:')),
            'givenName' => $helper->ask($input, $output, new Question('Given Name: ')),
            'familyName'=> $helper->ask($input, $output, new Question('Family Name: ')),
        ];


        $user = $this->store->create('core-user');
        foreach ($kvs as $k => $v) {
            $user->set($k, $v);
        }
        $user->save();

        $output->writeln(sprintf('<info>Created user "%s"</info>', $user->getId()));
    }
}

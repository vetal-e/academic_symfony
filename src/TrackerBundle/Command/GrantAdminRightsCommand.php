<?php

namespace TrackerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TrackerBundle\Entity\User;

class GrantAdminRightsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('trackerBundle:grantAdminRights')
            ->setDescription('Grant admin rights to username')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Who do you want grant admin rights to?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        if (!empty($username)) {
            $text = $this->grantAdminRights($username);
        } else {
            $text = 'Username parameter is required';
        }

        $output->writeln($text);
    }

    private function grantAdminRights($username)
    {
        $userRepository = $this->getContainer()->get('doctrine')->getRepository('TrackerBundle:User');
        /** @var User $user */
        $user = $userRepository->findOneByUsername($username);
        if (empty($user)) {
            return 'There is no such user';
        }

        $user->addRole('ROLE_ADMIN');
        $this->getContainer()->get('doctrine')->getManager()->flush();

        return 'Admin rights granted to ' . $user->getUsername();
    }
}

<?php

namespace TrackerBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry;
use TrackerBundle\Entity\User;

class MenuExtension extends \Twig_Extension
{
    private $userRepository;

    public function __construct(Registry $doctrine)
    {
        $this->userRepository = $doctrine->getRepository('TrackerBundle:User');
    }

    public function getName()
    {
        return 'menu_extension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getUserProjects', [$this, 'getUserProjects']),
            new \Twig_SimpleFunction('getUserIssues', [$this, 'getUserIssues']),
        ];
    }

    public function getUserProjects(User $user)
    {
        return $this->userRepository->getUserProjects($user);
    }

    public function getUserIssues(User $user)
    {
        return $this->userRepository->getUserIssues($user);
    }
}

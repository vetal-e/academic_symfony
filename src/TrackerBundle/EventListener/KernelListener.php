<?php

namespace TrackerBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class KernelListener
{
    protected $doctrine;
    protected $tokenStorage;

    /**
     * @param Registry $doctrine
     * @param TokenStorage $tokenStorage
     */
    public function __construct(Registry $doctrine, TokenStorage $tokenStorage)
    {
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $parameters = $event->getControllerResult();

        $user = $this->tokenStorage->getToken()->getUser();
        $userRepository = $this->doctrine->getRepository('TrackerBundle:User');

        $userProjects = $userRepository->getUserProjects($user);
        $userIssues = $userRepository->getUserIssues($user);

        $additionalParameters = [
            'menuProjects' => $userProjects,
            'menuIssues' => $userIssues,
        ];

        $event->setControllerResult($additionalParameters + $parameters);
    }
}

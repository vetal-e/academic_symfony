<?php

namespace TrackerBundle\Manager;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TrackerBundle\Entity\Activity;
use TrackerBundle\Entity\User;

class MailManager
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailer()
    {
        return $this->container->get('mailer');
    }

    /**
     * @return TwigEngine
     */
    protected function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * @param User $user
     * @param Activity $activity
     */
    public function sendActivityEmail(User $user, Activity $activity)
    {
        $contentReadable = $activity->getContentReadable();

        $message = \Swift_Message::newInstance()
            ->setSubject('Tracker Activity')
            ->setFrom('robot@trackerbundle.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->getTemplating()->render(
                    'emails/activity.html.twig',
                    ['content' => $contentReadable]
                ),
                'text/html'
            )
        ;

        $this->getMailer()->send($message);
    }
}
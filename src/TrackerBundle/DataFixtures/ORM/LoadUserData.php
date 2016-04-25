<?php

namespace TrackerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TrackerBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $admin */
        $admin = $userManager->createUser();
        /** @var User $manager */
        $manager = $userManager->createUser();
        /** @var User $operator */
        $operator = $userManager->createUser();

        $admin->setEmail('admin@trackerbundle.com');
        $admin->setUsername('trackerbundleAdmin');
        $admin->setPlainPassword('trackerbundleAdmin');
        $admin->setFullName('Joe Bower');
        $admin->setEnabled(true);

        $manager->setEmail('manager@trackerbundle.com');
        $manager->setUsername('trackerbundleManager');
        $manager->setPlainPassword('trackerbundleManager');
        $manager->setFullName('Justin Poole');
        $manager->setEnabled(true);

        $operator->setEmail('operator@trackerbundle.com');
        $operator->setUsername('trackerbundleOperator');
        $operator->setPlainPassword('trackerbundleOperator');
        $operator->setFullName('Audrey Vaughan');
        $operator->setEnabled(true);

        $userManager->updateUser($admin);
        $userManager->updateUser($manager);
        $userManager->updateUser($operator);
    }
}
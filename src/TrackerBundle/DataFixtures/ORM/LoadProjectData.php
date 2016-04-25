<?php

namespace TrackerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TrackerBundle\Entity\Project;

class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setLabel('Conquer the Earth!');
        $project->setCode('CTE');
        $project->setSummary(<<<EOT
In order to address this issue, we use compact modalities to show that evolutionary programming and architecture can collaborate to fix this quandary. However, embedded communication might not be the panacea that biologists expected. Furthermore, existing permutable and cooperative algorithms use lossless communication to locate the deployment of the transistor. Though prior solutions to this grand challenge are good, none have taken the trainable solution we propose here. Though conventional wisdom states that this challenge is usually solved by the study of thin clients, we believe that a different approach is necessary. Combined with RPCs, it investigates an analysis of reinforcement learning.
Read-write frameworks are particularly typical when it comes to random models. This is crucial to the success of our work. Nevertheless, permutable symmetries might not be the panacea that electrical engineers expected. On a similar note, indeed, simulated annealing and digital-to-analog converters have a long history of agreeing in this manner. Indeed, the producer-consumer problem and access points have a long history of agreeing in this manner.
EOT
        );
        $project->addMember($this->getReference('operatorUser'));

        $manager->persist($project);
        $manager->flush();

        $this->addReference('project', $project);
    }

    public function getOrder()
    {
        return 2;
    }
}

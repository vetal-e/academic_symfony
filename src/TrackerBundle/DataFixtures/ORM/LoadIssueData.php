<?php

namespace TrackerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TrackerBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $issue = new Issue();
        $issue->setSummary('Get things done');
        $issue->setCode('CTE-01');

        // @codingStandardsIgnoreStart
        $issue->setDescription(<<<EOT
Knowledge nay estimable questions repulsive daughters boy. Solicitude gay way unaffected expression for. His mistress ladyship required off horrible disposed rejoiced. Unpleasing pianoforte unreserved as oh he unpleasant no inquietude insipidity. Advantages can discretion possession add favourable cultivated admiration far. Why rather assure how esteem end hunted nearer and before. By an truth after heard going early given he. Charmed to it excited females whether at examine. Him abilities suffering may are yet dependent.
Woody equal ask saw sir weeks aware decay. Entrance prospect removing we packages strictly is no smallest he. For hopes may chief get hours day rooms. Oh no turned behind polite piqued enough at. Forbade few through inquiry blushes you. Cousin no itself eldest it in dinner latter missed no. Boisterous estimating interested collecting get conviction friendship say boy. Him mrs shy article smiling respect opinion excited. Welcomed humoured rejoiced peculiar to in an.
EOT
        );
        // @codingStandardsIgnoreEnd

        $issue->setType('TYPE_TASK');
        $issue->setPriority('PRIORITY_NORMAL');
        $issue->getStatus('STATUS_OPEN');
        $issue->setProject($this->getReference('project'));
        $issue->setReporter($this->getReference('operatorUser'));

        $manager->persist($issue);
        $manager->flush();

        $this->addReference('issue', $issue);
    }

    public function getOrder()
    {
        return 3;
    }
}

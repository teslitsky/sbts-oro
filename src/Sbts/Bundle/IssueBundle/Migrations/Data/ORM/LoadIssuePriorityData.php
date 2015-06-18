<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Sbts\Bundle\IssueBundle\Entity\IssuePriority;

class LoadIssuePriorityData extends AbstractFixture
{
    /**
     * @var array
     */
    private $data = [
        IssuePriority::PRIORITY_BLOCKER,
        IssuePriority::PRIORITY_CRITICAL,
        IssuePriority::PRIORITY_MAJOR,
        IssuePriority::PRIORITY_MINOR,
        IssuePriority::PRIORITY_TRIVIAL,
    ];

    /**
     * Load sample issue priority
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $name) {
            if (!$this->isEntityExist($manager, $name)) {
                $entity = new IssuePriority();
                $entity->setName($name);
                $entity->setLabel(ucfirst($name));

                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * Check if entity with this name already exist
     *
     * @param ObjectManager $manager
     * @param               $name
     *
     * @return bool
     */
    public function isEntityExist(ObjectManager $manager, $name)
    {
        $priority = $manager->getRepository('SbtsIssueBundle:IssuePriority')->findOneBy(['name' => $name]);

        return null !== $priority;
    }
}

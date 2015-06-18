<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Sbts\Bundle\IssueBundle\Entity\IssueResolution;

class LoadIssueResolutionData extends AbstractFixture
{
    /**
     * @var array
     */
    private $data = [
        IssueResolution::RESOLUTION_FIXED,
        IssueResolution::RESOLUTION_UNRESOLVED,
    ];

    /**
     * Load sample issue resolution
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $name) {
            if (!$this->isEntityExist($manager, $name)) {
                $entity = new IssueResolution();
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
        $priority = $manager->getRepository('SbtsIssueBundle:IssueResolution')->findOneBy(['name' => $name]);

        return null !== $priority;
    }
}

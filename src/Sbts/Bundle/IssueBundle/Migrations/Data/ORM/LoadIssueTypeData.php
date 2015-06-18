<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Sbts\Bundle\IssueBundle\Entity\IssueType;

class LoadIssueTypeData extends AbstractFixture
{
    /**
     * @var array
     */
    private $data = [
        IssueType::TYPE_BUG,
        IssueType::TYPE_STORY,
        IssueType::TYPE_SUB_TASK,
        IssueType::TYPE_TASK,
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
                $entity = new IssueType();
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
        $priority = $manager->getRepository('SbtsIssueBundle:IssueType')->findOneBy(['name' => $name]);

        return null !== $priority;
    }
}

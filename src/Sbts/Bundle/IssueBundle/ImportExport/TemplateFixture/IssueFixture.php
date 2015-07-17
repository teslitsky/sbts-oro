<?php

namespace Sbts\Bundle\IssueBundle\ImportExport\TemplateFixture;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class IssueFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'Sbts\Bundle\IssueBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('Story Issue');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Issue();
    }

    /**
     * @param string $key
     * @param Issue  $entity
     */
    public function fillEntityData($key, $entity)
    {
        $userRepo = $this->templateManager
            ->getEntityRepository('Oro\Bundle\UserBundle\Entity\User');
        $organizationRepo = $this->templateManager
            ->getEntityRepository('Oro\Bundle\OrganizationBundle\Entity\Organization');

        switch ($key) {
            case 'Story Issue':
                $entity
                    ->setCode('SBTS-1')
                    ->setSummary($key)
                    ->setDescription('Story description')
                    ->setIssueType(Issue::TYPE_STORY)
                    ->setIssuePriority(Issue::PRIORITY_MAJOR)
                    ->setIssueResolution(Issue::RESOLUTION_FIXED)
                    ->setReporter($userRepo->getEntity('John Doo'))
                    ->setOwner($userRepo->getEntity('John Doo'))
                    ->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')))
                    ->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')))
                    ->setOrganization($organizationRepo->getEntity('default'));

                return;

            case 'Sub-task Issue':
                $entity
                    ->setCode('SBTS-2')
                    ->setSummary($key)
                    ->setDescription('Sub-task description')
                    ->setIssueType(Issue::TYPE_SUB_TASK)
                    ->setIssuePriority(Issue::PRIORITY_MAJOR)
                    ->setIssueResolution(Issue::RESOLUTION_FIXED)
                    ->setReporter($userRepo->getEntity('John Doo'))
                    ->setOwner($userRepo->getEntity('John Doo'))
                    ->setParent($this->getEntity('Story Issue'))
                    ->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')))
                    ->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')))
                    ->setOrganization($organizationRepo->getEntity('default'));

                return;
        }

        parent::fillEntityData($key, $entity);
    }
}

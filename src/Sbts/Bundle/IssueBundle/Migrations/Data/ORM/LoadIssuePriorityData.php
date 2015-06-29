<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class LoadIssuePriorityData extends AbstractEnumFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getData()
    {
        return [
            Issue::PRIORITY_BLOCKER  => 'Blocker',
            Issue::PRIORITY_CRITICAL => 'Critical',
            Issue::PRIORITY_MAJOR    => 'Major',
            Issue::PRIORITY_MINOR    => 'Minor',
            Issue::PRIORITY_TRIVIAL  => 'Trivial',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnumCode()
    {
        return 'issue_priority';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultValue()
    {
        return Issue::PRIORITY_MAJOR;
    }
}

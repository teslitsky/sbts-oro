<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class LoadIssueResolutionData extends AbstractEnumFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getData()
    {
        return [
            Issue::RESOLUTION_UNRESOLVED => 'Unresolved',
            Issue::RESOLUTION_FIXED      => 'Fixed',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnumCode()
    {
        return 'issue_resolution';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultValue()
    {
        return Issue::RESOLUTION_UNRESOLVED;
    }
}

<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class LoadIssueTypeData extends AbstractEnumFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getData()
    {
        return [
            Issue::TYPE_STORY    => 'Story',
            Issue::TYPE_BUG      => 'Bug',
            Issue::TYPE_TASK     => 'Task',
            Issue::TYPE_SUB_TASK => 'Sub-task',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnumCode()
    {
        return 'issue_type';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultValue()
    {
        return Issue::TYPE_STORY;
    }
}

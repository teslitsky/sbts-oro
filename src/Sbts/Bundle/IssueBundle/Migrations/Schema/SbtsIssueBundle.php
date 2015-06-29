<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class SbtsIssueBundle implements
    Installation,
    ExtendExtensionAwareInterface
{
    const ISSUE_TABLE_NAME = 'sbts_issue';

    /**
     * @var ExtendExtension
     */
    protected $extendExtension;

    /**
     * {@inheritdoc}
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createIssueTable($schema);
        $this->updateIssueTable($schema);
    }

    /**
     * Create sbts_issue table
     *
     * @param Schema $schema
     */
    public function createIssueTable(Schema $schema)
    {
        $table = $schema->createTable(self::ISSUE_TABLE_NAME);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');

        $table->setPrimaryKey(['id']);
    }

    /**
     * Add ENUM fields for Issue
     *
     * @param Schema $schema
     */
    public function updateIssueTable(Schema $schema)
    {
        $this->extendExtension->addEnumField(
            $schema,
            self::ISSUE_TABLE_NAME,
            'issue_priority',
            'issue_priority'
        );

        $this->extendExtension->addEnumField(
            $schema,
            self::ISSUE_TABLE_NAME,
            'issue_type',
            'issue_type'
        );

        $this->extendExtension->addEnumField(
            $schema,
            self::ISSUE_TABLE_NAME,
            'issue_resolution',
            'issue_resolution'
        );
    }
}

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
    const USER_TABLE_NAME = 'oro_user';
    const ORGANIZATION_TABLE_NAME = 'oro_organization';

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
        $this->addIssueForeignKeys($schema);
    }

    /**
     * Create sbts_issue table
     *
     * @param Schema $schema
     */
    protected function createIssueTable(Schema $schema)
    {
        $table = $schema->createTable(self::ISSUE_TABLE_NAME);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
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
    protected function updateIssueTable(Schema $schema)
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

    /**
     * Add sbts_issue foreign keys
     *
     * @param Schema $schema
     */
    protected function addIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('sbts_issue');

        $table->addForeignKeyConstraint(
            $schema->getTable(self::USER_TABLE_NAME),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );

        $table->addForeignKeyConstraint(
            $schema->getTable(self::USER_TABLE_NAME),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );

        $table->addForeignKeyConstraint(
            $schema->getTable(self::ISSUE_TABLE_NAME),
            ['parent_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );

        $table->addForeignKeyConstraint(
            $schema->getTable(self::ORGANIZATION_TABLE_NAME),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
    }
}

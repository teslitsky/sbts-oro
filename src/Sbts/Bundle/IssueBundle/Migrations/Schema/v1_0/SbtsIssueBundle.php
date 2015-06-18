<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class SbtsIssueBundle implements Migration
{
    /**
     * @inheritdoc
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
        $this->createIssueTypeTable($schema);
        $this->createIssueResolutionTable($schema);
        $this->createIssuePriorityTable($schema);
        $this->createIssueForeignKeys($schema);
    }

    /**
     * Generate table sbts_issue_table
     *
     * @param Schema $schema
     */
    public function createIssueTable(Schema $schema)
    {
        if ($schema->hasTable('sbts_issue')) {
            $schema->dropTable('sbts_issue');
        }

        $table = $schema->createTable('sbts_issue');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('type_id', 'integer', ['notnull' => false]);
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');

        $table->setPrimaryKey(['id']);
    }

    /**
     * Generate table sbts_issue_type_table
     *
     * @param Schema $schema
     */
    public function createIssueTypeTable(Schema $schema)
    {
        if ($schema->hasTable('sbts_issue_type')) {
            $schema->dropTable('sbts_issue_type');
        }

        $table = $schema->createTable('sbts_issue_type');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name']);
    }

    /**
     * Generate table sbts_issue_resolution_table
     *
     * @param Schema $schema
     */
    public function createIssueResolutionTable(Schema $schema)
    {
        if ($schema->hasTable('sbts_issue_resolution')) {
            $schema->dropTable('sbts_issue_resolution');
        }

        $table = $schema->createTable('sbts_issue_resolution');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name']);
    }

    /**
     * Generate table sbts_issue_priority_table
     *
     * @param Schema $schema
     */
    public function createIssuePriorityTable(Schema $schema)
    {
        if ($schema->hasTable('sbts_issue_priority')) {
            $schema->dropTable('sbts_issue_priority');
        }

        $table = $schema->createTable('sbts_issue_priority');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name']);
    }

    public function createIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('sbts_issue');

        $table->addForeignKeyConstraint(
            $schema->getTable('sbts_issue_type'),
            ['type_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('sbts_issue_resolution'),
            ['resolution_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('sbts_issue_priority'),
            ['priority_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
    }
}

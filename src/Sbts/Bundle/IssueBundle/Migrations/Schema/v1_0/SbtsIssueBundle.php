<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class SbtsIssueBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createIssueTable($schema);
        $this->createIssueTypeTable($schema);
        $this->createIssueResolutionTable($schema);
        $this->createIssuePriorityTable($schema);
    }

    /**
     * Generate table sbts_issue_table
     *
     * @param Schema $schema
     */
    public function createIssueTable(Schema $schema)
    {
        $table = $schema->createTable('sbts_issue_table');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('type_id', 'integer');
        $table->addColumn('resolution_id', 'integer');
        $table->addColumn('priority_id', 'integer');
        $table->addColumn('reporter_id', 'integer');
        $table->addColumn('assignee_id', 'integer');
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
        $table = $schema->createTable('sbts_issue_type_table');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'text', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['id']);
    }

    /**
     * Generate table sbts_issue_resolution_table
     *
     * @param Schema $schema
     */
    public function createIssueResolutionTable(Schema $schema)
    {
        $table = $schema->createTable('sbts_issue_resolution_table');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'text', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['id']);
    }

    /**
     * Generate table sbts_issue_priority_table
     *
     * @param Schema $schema
     */
    public function createIssuePriorityTable(Schema $schema)
    {
        $table = $schema->createTable('sbts_issue_priority_table');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'text', ['notnull' => true, 'length' => 255]);

        $table->setPrimaryKey(['id']);
    }
}

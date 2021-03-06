<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class SbtsIssueBundleInstaller implements
    Installation,
    ExtendExtensionAwareInterface,
    NoteExtensionAwareInterface,
    ActivityExtensionAwareInterface
{
    const ISSUE_TABLE_NAME = 'sbts_issue';
    const USER_TABLE_NAME = 'oro_user';
    const ORGANIZATION_TABLE_NAME = 'oro_organization';
    const ISSUE_COLLABORATORS_TABLE_NAME = 'sbts_issue_to_collaborator';
    const ISSUE_RELATIONS_TABLE_NAME = 'sbts_issue_to_issue';
    const WORKFLOW_ITEM_TABLE_NAME = 'oro_workflow_item';
    const WORKFLOW_STEP_TABLE_NAME = 'oro_workflow_step';

    /**
     * @var ExtendExtension
     */
    protected $extendExtension;

    /**
     * @var NoteExtension
     */
    protected $noteExtension;

    /**
     * @var ActivityExtension
     */
    protected $activityExtension;

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
    public function setNoteExtension(NoteExtension $noteExtension)
    {
        $this->noteExtension = $noteExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
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

        $this->createIssueToCollaboratorTable($schema);
        $this->createIssueToIssueTable($schema);

        $this->addIssueForeignKeys($schema);
        $this->addIssueToCollaboratorForeignKeys($schema);
        $this->addIssueToIssueForeignKeys($schema);
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
        $table->addColumn('code', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code'], 'UNIQ_50ACC41277153098');
        $table->addIndex(['reporter_id'], 'idx_sbts_issue_reporter_id', []);
        $table->addIndex(['owner_id'], 'idx_sbts_issue_owner_id', []);
        $table->addIndex(['parent_id'], 'idx_sbts_issue_parent_id', []);
        $table->addIndex(['organization_id'], 'idx_sbts_issue_organization_id', []);
        $table->addIndex(['workflow_step_id'], 'IDX_50ACC41271FE882C', []);
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

        $this->noteExtension->addNoteAssociation($schema, self::ISSUE_TABLE_NAME);
        $this->activityExtension->addActivityAssociation($schema, 'oro_email', self::ISSUE_TABLE_NAME);
    }

    /**
     * Create sbts_issue_to_collaborator table
     *
     * @param Schema $schema
     */
    protected function createIssueToCollaboratorTable(Schema $schema)
    {
        $table = $schema->createTable(self::ISSUE_COLLABORATORS_TABLE_NAME);

        $table->addColumn('issue_id', 'integer');
        $table->addColumn('user_id', 'integer');

        $table->setPrimaryKey(['issue_id', 'user_id']);
        $table->addIndex(['issue_id'], 'IDX_CBCCC8725E7AA58C');
        $table->addIndex(['user_id'], 'IDX_CBCCC872A76ED395');
    }

    /**
     * Create sbts_issue_to_issue table
     *
     * @param Schema $schema
     */
    protected function createIssueToIssueTable(Schema $schema)
    {
        $table = $schema->createTable(self::ISSUE_RELATIONS_TABLE_NAME);

        $table->addColumn('issue_id', 'integer');
        $table->addColumn('linked_issue_id', 'integer');

        $table->setPrimaryKey(['issue_id', 'linked_issue_id']);
        $table->addIndex(['issue_id'], 'IDX_C70EC5B95E7AA58C');
        $table->addIndex(['linked_issue_id'], 'IDX_C70EC5B9307AEB53');
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

        $table->addForeignKeyConstraint(
            $schema->getTable(self::WORKFLOW_ITEM_TABLE_NAME),
            ['workflow_item_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable(self::WORKFLOW_STEP_TABLE_NAME),
            ['workflow_step_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Adds sbts_issue_to_collaborator foreign keys
     *
     * @param Schema $schema
     */
    protected function addIssueToCollaboratorForeignKeys(Schema $schema)
    {
        $table = $schema->getTable(self::ISSUE_COLLABORATORS_TABLE_NAME);

        $table->addForeignKeyConstraint(
            $schema->getTable('sbts_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }

    /**
     * Adds sbts_issue_to_issue foreign keys
     *
     * @param Schema $schema
     */
    protected function addIssueToIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable(self::ISSUE_RELATIONS_TABLE_NAME);

        $table->addForeignKeyConstraint(
            $schema->getTable('sbts_issue'),
            ['linked_issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('sbts_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}

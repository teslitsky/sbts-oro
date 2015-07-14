<?php

namespace Sbts\Bundle\IssueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

use Sbts\Bundle\IssueBundle\Model\ExtendIssue;

/**
 * @ORM\Table(name="sbts_issue")
 * @ORM\Entity(repositoryClass="Sbts\Bundle\IssueBundle\Entity\Repository\IssueRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="sbts_issue_index",
 *      routeView="sbts_issue_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-tasks"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "workflow"={
 *              "active_workflow"="issue_flow",
 *              "show_step_in_grid"=false
 *          },
 *          "security"={
 *              "type"="ACL"
 *          },
 *          "form"={
 *              "form_type"="sbts_issue_select",
 *              "grid_name"="issues-grid"
 *          }
 *      }
 * )
 */
class Issue extends ExtendIssue
{
    const PRIORITY_BLOCKER = 'blocker';
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_MAJOR = 'major';
    const PRIORITY_MINOR = 'minor';
    const PRIORITY_TRIVIAL = 'trivial';

    const RESOLUTION_UNRESOLVED = 'unresolved';
    const RESOLUTION_FIXED = 'fixed';

    const TYPE_BUG = 'bug';
    const TYPE_SUB_TASK = 'sub_task';
    const TYPE_TASK = 'task';
    const TYPE_STORY = 'story';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255)
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false, unique=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reporter;

    /**
     * @var User Assignee user
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(
     *      name="sbts_issue_to_collaborator",
     *      joinColumns={
     *          @ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *      }
     * )
     */
    protected $collaborators;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Issue")
     * @ORM\JoinTable(
     *      name="sbts_issue_to_issue",
     *      joinColumns={
     *          @ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="linked_issue_id", referencedColumnName="id", onDelete="CASCADE")
     *      }
     * )
     */
    protected $related;

    /**
     * @var Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     */
    protected $children;

    /**
     * @var WorkflowItem
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
     * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $workflowItem;

    /**
     * @var WorkflowStep
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
     * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $workflowStep;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        parent::__construct();

        $this->collaborators = new ArrayCollection();
        $this->related = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * Gets id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets summary
     *
     * @param string $summary
     *
     * @return self
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Gets summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Sets issue code
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets issue code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets reporter
     *
     * @param User $reporter
     *
     * @return self
     */
    public function setReporter(User $reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Gets reporter
     *
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Adds collaborator
     *
     * @param User $user
     *
     * @return Issue
     */
    public function addCollaborator(User $user)
    {
        if (!$this->hasCollaborator($user)) {
            $this->collaborators->add($user);
        }

        return $this;
    }

    /**
     * Removes collaborator
     *
     * @param User $user
     */
    public function removeCollaborator(User $user)
    {
        $this->collaborators->removeElement($user);
    }

    /**
     * Has collaborator
     *
     * @param User $user
     *
     * @return  boolean
     */
    public function hasCollaborator(User $user)
    {
        return $this->collaborators->contains($user);
    }

    /**
     *  Gets collaborators
     *
     * @return ArrayCollection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Adds related issue
     *
     * @param Issue $issue
     *
     * @return self
     */
    public function addRelated(Issue $issue)
    {
        if (!$this->hasRelated($issue)) {
            $this->related->add($issue);
        }

        return $this;
    }

    /**
     * Removes related issue
     *
     * @param Issue $issue
     */
    public function removeRelated(Issue $issue)
    {
        $this->related->removeElement($issue);
    }

    /**
     * Has related issue
     *
     * @param Issue $issue
     *
     * @return boolean
     */
    public function hasRelated(Issue $issue)
    {
        return $this->related->contains($issue);
    }

    /**
     *  Gets related issues
     *
     * @return ArrayCollection
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Sets parent
     *
     * @param Issue $parent
     *
     * @return self
     */
    public function setParent(Issue $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets parent
     *
     * @return Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Adds child
     *
     * @param Issue $children
     *
     * @return self
     */
    public function addChild(Issue $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Removes child
     *
     * @param Issue $children
     */
    public function removeChild(Issue $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Gets children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets item workflow item
     *
     * @param WorkflowItem $workflowItem
     *
     * @return self
     */
    public function setWorkflowItem($workflowItem)
    {
        $this->workflowItem = $workflowItem;

        return $this;
    }

    /**
     * Gets item workflow item
     *
     * @return WorkflowItem
     */
    public function getWorkflowItem()
    {
        return $this->workflowItem;
    }

    /**
     * Sets item workflow step
     *
     * @param WorkflowItem $workflowStep
     *
     * @return self
     */
    public function setWorkflowStep($workflowStep)
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    /**
     * Sets item workflow step
     *
     * @return WorkflowStep
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * Sets created
     *
     * @param \DateTime $created
     *
     * @return self
     */
    public function setCreatedAt($created)
    {
        $this->createdAt = $created;

        return $this;
    }

    /**
     * Gets created
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updated
     *
     * @param \DateTime $updated
     *
     * @return self
     */
    public function setUpdatedAt($updated)
    {
        $this->updatedAt = $updated;

        return $this;
    }

    /**
     * Gets updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets owner
     *
     * @param User $owner
     *
     * @return self
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Gets owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets organization
     *
     * @param Organization $organization
     *
     * @return self
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Gets organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Checks if issue is story
     *
     * @return bool
     */
    public function isStory()
    {
        return $this->getIssueType()->getId() === self::TYPE_STORY;
    }

    /**
     * Checks if issue is sub-task
     *
     * @return bool
     */
    public function isSubTask()
    {
        return $this->getIssueType()->getId() === self::TYPE_SUB_TASK;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdateAction()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersistAction()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->generateCode();
    }

    /**
     * Generate issue code if not specified
     */
    public function generateCode()
    {
        if (!$this->getCode() && $this->getOrganization()) {
            $this->setCode(sprintf('%s-%d', $this->getOrganization()->getName(), uniqid()));
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getCode();
    }
}

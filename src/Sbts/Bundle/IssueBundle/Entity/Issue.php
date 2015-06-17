<?php

namespace Sbts\Bundle\IssueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\UserBundle\Entity\User;

use Sbts\Bundle\IssueBundle\Model\ExtendIssue;

/**
 * @ORM\Table(name="sbts_issue")
 * @ORM\Entity(repositoryClass="Sbts\Bundle\IssueBundle\Entity\Repository\IssueRepository")
 * @Config
 */
class Issue extends ExtendIssue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var IssueType
     *
     * @ORM\ManyToOne(targetEntity="IssueType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $type;

    /**
     * @var IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="IssuePriority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $priority;

    /**
     * @var IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="IssueResolution")
     * @ORM\JoinColumn(name="resolution_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $resolution;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $reporter;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $assignee;

    /**
     * @var Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     */
    private $children;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated;

    public function __construct()
    {
        parent::__construct();

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
     * Gets issue code
     *
     * @return string
     */
    public function getCode()
    {
        return '';
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
     * Sets type
     *
     * @param IssueType $type
     *
     * @return self
     */
    public function setType(IssueType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets type
     *
     * @return IssueType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets priority
     *
     * @param IssuePriority $priority
     *
     * @return self
     */
    public function setPriority(IssuePriority $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Gets priority
     *
     * @return IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets resolution
     *
     * @param IssueResolution $resolution
     *
     * @return self
     */
    public function setResolution(IssueResolution $resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Gets resolution
     *
     * @return IssueResolution
     */
    public function getResolution()
    {
        return $this->resolution;
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
     * Sets assignee
     *
     * @param User $assignee
     *
     * @return self
     */
    public function setAssignee(User $assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Gets assignee
     *
     * @return User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Sets parent
     *
     * @param Issue $parent
     *
     * @return User
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
     * Sets created
     *
     * @param \DateTime $created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Gets created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets updated
     *
     * @param \DateTime $updated
     *
     * @return self
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Gets updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}

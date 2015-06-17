<?php

namespace Sbts\Bundle\IssueBundle\Entity;

/**
 * @ORM\Table(name="sbts_issue_priority")
 * @ORM\Entity
 */
class IssuePriority
{
    const PRIORITY_BLOCKER = 'blocker';
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_MAJOR = 'major';
    const PRIORITY_MINOR = 'minor';
    const PRIORITY_TRIVIAL = 'trivial';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;


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
     * Sets name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}

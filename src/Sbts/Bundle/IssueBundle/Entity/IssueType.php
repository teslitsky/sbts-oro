<?php

namespace Sbts\Bundle\IssueBundle\Entity;

/**
 * @ORM\Table(name="sbts_issue_type")
 * @ORM\Entity
 */
class IssueType
{
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
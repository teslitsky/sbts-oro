<?php

namespace Sbts\Bundle\IssueBundle\Model;

use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;

/**
 * Methods for Issue extend fields
 */
trait ExtendParamsTrait
{
    /**
     * @param AbstractEnumValue $value
     * @return self
     */
    public function setIssueType($value)
    {
    }

    /**
     * @return AbstractEnumValue
     */
    public function getIssueType()
    {
    }

    /**
     * @param AbstractEnumValue $value
     * @return self
     */
    public function setIssuePriority($value)
    {
    }

    /**
     * @return AbstractEnumValue
     */
    public function getIssuePriority()
    {
    }

    /**
     * @param AbstractEnumValue $value
     * @return self
     */
    public function setIssueResolution($value)
    {
    }

    /**
     * @return AbstractEnumValue
     */
    public function getIssueResolution()
    {
    }
}

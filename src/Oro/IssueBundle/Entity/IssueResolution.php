<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IssueStatus
 *
 * @ORM\Table(name="oro_issue_resolution")
 * @ORM\Entity
 */
class IssueResolution
{
    const RESOLUTION_INCOMPLETE = 'incomplete';
    const RESOLUTION_DUPLICATE = 'duplicate';
    const RESOLUTION_WONTFIX = 'wontfix';
    const RESOLUTION_FIXED = 'fixed';
    const RESOLUTION_DONE = 'done';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     * @ORM\Id
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return IssueStatus
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return IssueStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return IssueStatus
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get string value of resolution name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}

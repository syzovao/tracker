<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IssuePriority
 *
 * @ORM\Table(name="oro_issue_priority")
 * @ORM\Entity
 */
class IssuePriority
{
    const PRIORITY_TRIVIAL = 'trivial';
    const PRIORITY_MINOR = 'minor';
    const PRIORITY_MAJOR = 'major';
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_BLOCKER = 'blocker';

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
     * @return IssuePriority
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
     * @return IssuePriority
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
     * @return IssuePriority
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
}

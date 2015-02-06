<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oro\ProjectBundle\Entity\Project;
use Oro\UserBundle\Entity\User;

/**
 * Issue
 *
 * @ORM\Table(name="oro_issue")
 * @ORM\Entity
 */
class Issue
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
     * @ORM\Column(name="code", type="string", length=20, nullable=false)
     */
    private $code;

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
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssueType")
     * @ORM\JoinColumn(name="issue_type_code", referencedColumnName="code", onDelete="SET NULL")
     */
    private $issueType;

    /**
     * @var IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="issue_priority_code", referencedColumnName="code", onDelete="SET NULL")
     */
    private $issuePriority;

    /**
     * @var IssueStatus
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssueStatus")
     * @ORM\JoinColumn(name="issue_status_code", referencedColumnName="code", onDelete="SET NULL")
     */
    private $issueStatus;

    /**
     * @var IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="issue_resolution_code", referencedColumnName="code")
     */
    private $issueResolution;

    /**
     * @var string
     *
     * @ORM\Column(name="reporter_id", type="integer")
     */
    private $reporter;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $assignee;

    /**
     * @var string
     *
     * @ORM\Column(name="collaborators", type="string", length=255)
     */
    private $collaborators;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Oro\IssueBundle\Entity\Issue", mappedBy="parent")
     **/
    private $children;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Oro\ProjectBundle\Entity\Project", inversedBy="issues")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * Constructor
     */
    public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Issue
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
     * Set summary
     *
     * @param string $summary
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set reporter
     *
     * @param string $reporter
     * @return Issue
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return string 
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param integer $assignee
     * @return Issue
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return integer 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set collaborators
     *
     * @param string $collaborators
     * @return Issue
     */
    public function setCollaborators($collaborators)
    {
        $this->collaborators = $collaborators;

        return $this;
    }

    /**
     * Get collaborators
     *
     * @return string 
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set parent
     *
     * @param integer $parent
     * @return Issue
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return integer 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param integer $children
     * @return Issue
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return integer 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Issue
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Issue
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set issueType
     *
     * @param issueType $issueType
     * @return Issue
     */
    public function setIssueType($issueType)
    {
        $this->issueType = $issueType;

        return $this;
    }

    /**
     * Get IssueType
     *
     * @return IssueType
     */
    public function getIssueType()
    {
        return $this->issueType;
    }

    /**
     * Set IssuePriority
     *
     * @param IssuePriority $issuePriority
     * @return Issue
     */
    public function setIssuePriority($issuePriority)
    {
        $this->issuePriority = $issuePriority;

        return $this;
    }

    /**
     * Get IssuePriority
     *
     * @return IssuePriority
     */
    public function getIssuePriority()
    {
        return $this->issuePriority;
    }

    /**
     * Set IssueStatus
     *
     * @param string $issueStatus
     * @return IssueStatus
     */
    public function setIssueStatus($issueStatus)
    {
        $this->issueStatus = $issueStatus;

        return $this;
    }

    /**
     * Get IssueStatus
     *
     * @return IssueStatus
     */
    public function getIssueStatus()
    {
        return $this->issueStatus;
    }

    /**
     * Set IssueResolution
     *
     * @param IssueResolution $issueResolution
     * @return IssueResolution
     */
    public function setIssueResolution($issueResolution)
    {
        $this->issueResolution = $issueResolution;

        return $this;
    }

    /**
     * Get IssueResolution
     *
     * @return IssueResolution
     */
    public function getIssueResolution()
    {
        return $this->issueResolution;
    }

    /**
     * Set project
     *
     * @param Project $project
     * @return $this
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add children
     *
     * @param \Oro\IssueBundle\Entity\Issue $children
     * @return Issue
     */
    public function addChild(Issue $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Oro\IssueBundle\Entity\Issue $children
     */
    public function removeChild(Issue $children)
    {
        $this->children->removeElement($children);
    }
}

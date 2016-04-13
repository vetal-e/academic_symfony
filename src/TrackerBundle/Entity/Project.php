<?php

namespace TrackerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TrackerBundle\Entity\Repository\ProjectRepository")
 * @ORM\Table(name="project")
 */
class Project
{
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
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     */
    protected $label;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, unique=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    protected $summary;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="project")
     */
    protected $issues;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects")
     * @ORM\JoinTable(name="project_user",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->issues = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getCode() . ' ' . $this->getLabel();
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
     * Set label
     *
     * @param string $label
     * @return Project
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Project
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
     * @return Project
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
     * Add member
     *
     * @param User $member
     * @return Project
     */
    public function addMember(User $member)
    {
        $member->addProject($this);
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param User $member
     */
    public function removeMember(User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Add issue
     *
     * @param Issue $issue
     * @return Project
     */
    public function addIssue(Issue $issue)
    {
        $this->issues[] = $issue;

        return $this;
    }

    /**
     * Remove issue
     *
     * @param Issue $issue
     */
    public function removeIssue(Issue $issue)
    {
        $this->issues->removeElement($issue);
    }

    /**
     * Get issues
     *
     * @return ArrayCollection
     */
    public function getIssues()
    {
        return $this->issues;
    }
}

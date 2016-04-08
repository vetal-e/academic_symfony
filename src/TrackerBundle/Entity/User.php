<?php

namespace TrackerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FOSUser;

/**
 * @ORM\Entity(repositoryClass="TrackerBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User extends FOSUser
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
     * @ORM\Column(name="full_name", type="string", length=255, nullable=true)
     */
    protected $fullName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    protected $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="reporter")
     */
    protected $reportedIssues;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="assignee")
     */
    protected $assignedIssues;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="members")
     */
    protected $projects;

    /**
     * @ORM\ManyToMany(targetEntity="Issue", mappedBy="collaborators")
     */
    protected $issues;

    public function __construct()
    {
        parent::__construct();

        $this->comments = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->reportedIssues = new ArrayCollection();
        $this->assignedIssues = new ArrayCollection();
        $this->issues = new ArrayCollection();
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
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     * @return User
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add project
     *
     * @param Project $project
     * @return User
     */
    public function addProject(Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param Project $project
     */
    public function removeProject(Project $project)
    {
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return ArrayCollection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add reportedIssue
     *
     * @param Issue $reportedIssue
     * @return User
     */
    public function addReportedIssue(Issue $reportedIssue)
    {
        $this->reportedIssues[] = $reportedIssue;

        return $this;
    }

    /**
     * Remove reportedIssue
     *
     * @param Issue $reportedIssue
     */
    public function removeReportedIssue(Issue $reportedIssue)
    {
        $this->reportedIssues->removeElement($reportedIssue);
    }

    /**
     * Get reportedIssues
     *
     * @return ArrayCollection
     */
    public function getReportedIssues()
    {
        return $this->reportedIssues;
    }

    /**
     * Add assignedIssue
     *
     * @param Issue $assignedIssue
     * @return User
     */
    public function addAssignedIssue(Issue $assignedIssue)
    {
        $this->assignedIssues[] = $assignedIssue;

        return $this;
    }

    /**
     * Remove assignedIssue
     *
     * @param Issue $assignedIssue
     */
    public function removeAssignedIssue(Issue $assignedIssue)
    {
        $this->assignedIssues->removeElement($assignedIssue);
    }

    /**
     * Get assignedIssues
     *
     * @return ArrayCollection
     */
    public function getAssignedIssues()
    {
        return $this->assignedIssues;
    }

    /**
     * Add issue
     *
     * @param Issue $issue
     * @return User
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

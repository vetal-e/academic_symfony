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
    const ROLE_OPERATOR = 'Operator';
    const ROLE_MANAGER = 'Manager';
    const ROLE_ADMIN = 'Administrator';

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

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="user")
     */
    protected $generatedActivities;

    public function __construct()
    {
        parent::__construct();

        $this->comments = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->reportedIssues = new ArrayCollection();
        $this->assignedIssues = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->generatedActivities = new ArrayCollection();

        $this->addRole('ROLE_OPERATOR');
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

    /**
     * @param string $constName
     * @return string
     */
    public function getRoleName($constName)
    {
        try {
            return constant('self::' . $constName);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getRoleNames()
    {
        $userRoles = $this->getRoles();
        $orderedRoles = [
            'ROLE_OPERATOR',
            'ROLE_MANAGER',
            'ROLE_ADMIN',
        ];
        $orderedUserRoles = array_intersect($orderedRoles, $userRoles);

        $roleNames = [];
        foreach ($orderedUserRoles as $roleConst) {
            $roleName = $this->getRoleName($roleConst);
            if (!empty($roleName)) {
                $roleNames[] = $roleName;
            }
        }

        return $roleNames;
    }

    /**
     * Add generatedActivity
     *
     * @param Activity $generatedActivity
     * @return User
     */
    public function addGeneratedActivity(Activity $generatedActivity)
    {
        $this->generatedActivities[] = $generatedActivity;

        return $this;
    }

    /**
     * Remove generatedActivity
     *
     * @param Activity $generatedActivity
     */
    public function removeGeneratedActivity(Activity $generatedActivity)
    {
        $this->generatedActivities->removeElement($generatedActivity);
    }

    /**
     * Get generatedActivities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeneratedActivities()
    {
        return $this->generatedActivities;
    }
}

<?php

namespace TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TrackerBundle\Entity\Repository\ActivityRepository")
 * @ORM\Table(name="activity")
 * @ORM\HasLifecycleCallbacks
 */
class Activity
{
    const TYPE_ISSUE_CREATED = 'Issue created';
    const TYPE_ISSUE_STATUS_CHANGED = 'Issue status changed';
    const TYPE_ISSUE_COMMENT = 'New comment';
    const TYPE_ISSUE_COMMENT_UPDATED = 'Comment updated';

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255, nullable=true)
     */
    protected $content;

    /**
     * @var Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="activities")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $issue;

    /**
     * @var Comment
     *
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="activities")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

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
     * Set type
     *
     * @param string $type
     * @return Activity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Activity
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Activity
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
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Set issue
     *
     * @param Issue $issue
     * @return Activity
     */
    public function setIssue(Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set comment
     *
     * @param Issue $comment
     * @return Activity
     */
    public function setComment(Issue $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return Issue
     */
    public function getComment()
    {
        return $this->comment;
    }
}

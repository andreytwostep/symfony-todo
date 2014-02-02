<?php

namespace Dev\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity
 * @ORM\HasLifecycleCallBacks()
 */
class Task
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
     * @ORM\Column(name="task", type="string", length=255)
     */
    private $task;

    /**
     * @var boolean
     *
     * @ORM\Column(name="complete", type="boolean", nullable=true)
     */
    private $complete;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set task
     *
     * @param string $task
     * @return Task
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set complete
     * @param boolean $complete
     * @return Task
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;

        return $this;
    }

    /**
     * Get complete
     *
     * @return boolean
     */
    public function getComplete()
    {
        return $this->complete;
    }

    /**
     * Set created
	 *
     * @ORM\PrePersist
     * @param \DateTime $created
     * @return Task
     */
    public function setCreated()
    {
        if(!isset($this->created))
			$this->created = new \DateTime();

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}

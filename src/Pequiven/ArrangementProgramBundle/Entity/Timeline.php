<?php

namespace Pequiven\ArrangementProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Linea de tiempo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pequiven\ArrangementProgramBundle\Repository\TimelineRepository")
 */
class Timeline
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
     * Metas
     * @var \Pequiven\ArrangementProgramBundle\Entity\Goal
     * 
     * @ORM\OneToMany(targetEntity="Pequiven\ArrangementProgramBundle\Entity\Goal",mappedBy="timeline",cascade={"persist","remove"})
     */
    private $goals;
    
    /**
     * Estatus
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;

    /**
     * Programa de gestion
     * @var \Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram
     * 
     * @ORM\ManyToOne(targetEntity="Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram",inversedBy="timelines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $arrangementProgram;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->goals = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set status
     *
     * @param integer $status
     * @return Timeline
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add goals
     *
     * @param \Pequiven\ArrangementProgramBundle\Entity\Goal $goals
     * @return Timeline
     */
    public function addGoal(\Pequiven\ArrangementProgramBundle\Entity\Goal $goals)
    {
        $goals->setTimeline($this);
        $this->goals->add($goals);

        return $this;
    }

    /**
     * Remove goals
     *
     * @param \Pequiven\ArrangementProgramBundle\Entity\Goal $goals
     */
    public function removeGoal(\Pequiven\ArrangementProgramBundle\Entity\Goal $goals)
    {
        //$goals->setTimeline(null);
        $this->goals->removeElement($goals);
    }

    /**
     * Get goals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function &getGoals()
    {
        return $this->goals;
    }

    /**
     * Set arrangementProgram
     *
     * @param \Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram $arrangementProgram
     * @return Timeline
     */
    public function setArrangementProgram(\Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram $arrangementProgram = null)
    {
        $this->arrangementProgram = $arrangementProgram;

        return $this;
    }

    /**
     * Get arrangementProgram
     *
     * @return \Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram 
     */
    public function getArrangementProgram()
    {
        return $this->arrangementProgram;
    }
}
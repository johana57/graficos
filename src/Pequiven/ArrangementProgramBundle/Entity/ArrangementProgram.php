<?php

namespace Pequiven\ArrangementProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pequiven\ArrangementProgramBundle\Model\ArrangementProgram as Model;

/**
 * Programa de gestion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pequiven\ArrangementProgramBundle\Repository\ArrangementProgramRepository")
 */
class ArrangementProgram extends Model
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
     * Periodo.
     * @var \Pequiven\SEIPBundle\Entity\Period
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\Period")
     * @ORM\JoinColumn(nullable=false)
     */
    private $period;
    
    /**
     * Programa de gestión asociada.
     * @var \Pequiven\MasterBundle\Entity\ArrangementProgram\CategoryArrangementProgram
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\MasterBundle\Entity\ArrangementProgram\CategoryArrangementProgram")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoryArrangementProgram;

    /**
     * Objetivo táctico
     * @var \Pequiven\ObjetiveBundle\Entity\Objetive
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\ObjetiveBundle\Entity\Objetive")
     * @ORM\JoinColumn(name="tactical_objective_id")
     */
    private $tacticalObjective;

    /**
     * Objetivo operativo.
     * @var \Pequiven\ObjetiveBundle\Entity\Objetive
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\ObjetiveBundle\Entity\Objetive")
     * @ORM\JoinColumn(name="operational_objective_id")
     */
    private $operationalObjective;

    /**
     * Proceso
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255)
     */
    private $process;

    /**
     * Responsables del programa
     * @var \Pequiven\SEIPBundle\Entity\User
     *
     * @ORM\ManyToMany(targetEntity="Pequiven\SEIPBundle\Entity\User",inversedBy="arrangementPrograms")
     */
    private $responsibles;

    /**
     * Revisado por
     * @var \Pequiven\SEIPBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\User")
     */
    private $reviewedBy;

    /**
     * Fecha de revision
     * @var \DateTime
     *
     * @ORM\Column(name="revisionDate", type="datetime",nullable=true)
     */
    private $revisionDate;

    /**
     * Aprobado por
     * @var \Pequiven\SEIPBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\User")
     */
    private $approvedBy;

    /**
     * Fecha de aprobacion
     * @var \DateTime
     *
     * @ORM\Column(name="approvalDate", type="datetime",nullable=true)
     */
    private $approvalDate;

    /**
     * Tipo de programa de gestion
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;
    
    /**
     * Creado por
     * @var \Pequiven\SEIPBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;
 
    public function __construct() {
        $this->responsibles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set process
     *
     * @param string $process
     * @return ArrangementProgram
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string 
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set revisionDate
     *
     * @param \DateTime $revisionDate
     * @return ArrangementProgram
     */
    public function setRevisionDate($revisionDate)
    {
        $this->revisionDate = $revisionDate;

        return $this;
    }

    /**
     * Get revisionDate
     *
     * @return \DateTime 
     */
    public function getRevisionDate()
    {
        return $this->revisionDate;
    }

    /**
     * Set approvalDate
     *
     * @param \DateTime $approvalDate
     * @return ArrangementProgram
     */
    public function setApprovalDate($approvalDate)
    {
        $this->approvalDate = $approvalDate;

        return $this;
    }

    /**
     * Get approvalDate
     *
     * @return \DateTime 
     */
    public function getApprovalDate()
    {
        return $this->approvalDate;
    }

    /**
     * Set period
     *
     * @param \Pequiven\SEIPBundle\Entity\Period $period
     * @return ArrangementProgram
     */
    public function setPeriod(\Pequiven\SEIPBundle\Entity\Period $period = null)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return \Pequiven\SEIPBundle\Entity\Period 
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set categoryArrangementProgram
     *
     * @param \Pequiven\MasterBundle\Entity\ArrangementProgram\CategoryArrangementProgram $categoryArrangementProgram
     * @return ArrangementProgram
     */
    public function setCategoryArrangementProgram(\Pequiven\MasterBundle\Entity\ArrangementProgram\CategoryArrangementProgram $categoryArrangementProgram = null)
    {
        $this->categoryArrangementProgram = $categoryArrangementProgram;

        return $this;
    }

    /**
     * Get categoryArrangementProgram
     *
     * @return \Pequiven\MasterBundle\Entity\ArrangementProgram\CategoryArrangementProgram 
     */
    public function getCategoryArrangementProgram()
    {
        return $this->categoryArrangementProgram;
    }

    /**
     * Set tacticalObjective
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $tacticalObjective
     * @return ArrangementProgram
     */
    public function setTacticalObjective(\Pequiven\ObjetiveBundle\Entity\Objetive $tacticalObjective = null)
    {
        $this->tacticalObjective = $tacticalObjective;

        return $this;
    }

    /**
     * Get tacticalObjective
     *
     * @return \Pequiven\ObjetiveBundle\Entity\Objetive 
     */
    public function getTacticalObjective()
    {
        return $this->tacticalObjective;
    }

    /**
     * Set operationalObjective
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $operationalObjective
     * @return ArrangementProgram
     */
    public function setOperationalObjective(\Pequiven\ObjetiveBundle\Entity\Objetive $operationalObjective = null)
    {
        $this->operationalObjective = $operationalObjective;

        return $this;
    }

    /**
     * Get operationalObjective
     *
     * @return \Pequiven\ObjetiveBundle\Entity\Objetive 
     */
    public function getOperationalObjective()
    {
        return $this->operationalObjective;
    }

    /**
     * Set reviewedBy
     *
     * @param \Pequiven\SEIPBundle\Entity\User $reviewedBy
     * @return ArrangementProgram
     */
    public function setReviewedBy(\Pequiven\SEIPBundle\Entity\User $reviewedBy = null)
    {
        $this->reviewedBy = $reviewedBy;

        return $this;
    }

    /**
     * Get reviewedBy
     *
     * @return \Pequiven\SEIPBundle\Entity\User 
     */
    public function getReviewedBy()
    {
        return $this->reviewedBy;
    }

    /**
     * Set approvedBy
     *
     * @param \Pequiven\SEIPBundle\Entity\User $approvedBy
     * @return ArrangementProgram
     */
    public function setApprovedBy(\Pequiven\SEIPBundle\Entity\User $approvedBy = null)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy
     *
     * @return \Pequiven\SEIPBundle\Entity\User 
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
        
        return $this;
    }
    
    function getCreatedBy() {
        return $this->createdBy;
    }

    function setCreatedBy(\Pequiven\SEIPBundle\Entity\User $createdBy) {
        $this->createdBy = $createdBy;
        return $this;
    }
    
    function getTypeLabel() {
        
        $labels = array(
            self::TYPE_ARRANGEMENT_PROGRAM_TACTIC => 'pequiven.arrangement_program.type.tactic',
            self::TYPE_ARRANGEMENT_PROGRAM_OPERATIVE => 'pequiven.arrangement_program.type.operative',
        );
        if(isset($labels[$this->type])){
            return $labels[$this->type];
        }
    }

    /**
     * Add responsibles
     *
     * @param \Pequiven\SEIPBundle\Entity\User $responsibles
     * @return ArrangementProgram
     */
    public function addResponsible(\Pequiven\SEIPBundle\Entity\User $responsibles)
    {
        $this->responsibles->add($responsibles);

        return $this;
    }

    /**
     * Remove responsibles
     *
     * @param \Pequiven\SEIPBundle\Entity\User $responsibles
     */
    public function removeResponsible(\Pequiven\SEIPBundle\Entity\User $responsibles)
    {
        $this->responsibles->removeElement($responsibles);
    }

    /**
     * Get responsibles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponsibles()
    {
        return $this->responsibles;
    }
}

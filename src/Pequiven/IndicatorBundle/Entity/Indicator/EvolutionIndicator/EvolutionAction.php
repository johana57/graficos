<?php

namespace Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pequiven\IndicatorBundle\Model\Indicator\EvolutionIndicator\EvolutionAction as Model;

/**
 * Plan de Acción y Seguimiento del Indicador
 *
 * @author Maximo Sojo <maxsojo13@gmail.com>
 * @ORM\Table(name="seip_indicator_evolution_action")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EvolutionAction extends Model {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCause", inversedBy="indicatorAction")
     * @ORM\JoinColumn(name="cause_id", referencedColumnName="id")
     */
    private $evolutionCause;

    /**
     * @var string
     *
     * @ORM\Column(name="ref" , type="string", length=15)
     */
    private $ref;
    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStart", type="datetime", nullable=true)
     */
    private $dateStart;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="dateEnd", type="datetime", nullable=true)
     */
    private $dateEnd;
    /**
     * @var integer
     * 
     * @ORM\Column(name="advance", type="integer") 
     */
    private $advance;

    /**
     * @var string
     * 
     * @ORM\Column(name="observations", type="string", length=255)
     */
    private $observations;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * Usuario que creo la causa
     * 
     * @var \Pequiven\SEIPBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="\Pequiven\SEIPBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

     /**
     * @ORM\ManyToOne(targetEntity="\Pequiven\SIGBundle\Entity\TypeActionManagementSystem", inversedBy="typeAction")
     * @ORM\JoinColumn(name="typeAction_id", referencedColumnName="id")
     */
    private $indicatorAction;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="\Pequiven\IndicatorBundle\Entity\Indicator", inversedBy="indicatorAction")
     * @ORM\JoinColumn(name="indicator_id", referencedColumnName="id")
     */
    private $indicatorRel;

    /**
     * @ORM\ManyToOne(targetEntity="\Pequiven\SIGBundle\Entity\TypeVerificationManagementSystem", inversedBy="typeVerification")
     * @ORM\JoinColumn(name="typeVerification_id", referencedColumnName="id")
     */
    private $actionVerification;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set evolutionCause
     *
     * @param \Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCause $evolutionCause
     * @return Indicator
     */
    public function setEvolutionCause(\Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCause $evolutionCause) {
        
        $this->evolutionCause = $evolutionCause;

        return $this;
    }

    /**
     * Get evolutionCause
     *
     * @return Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCause
     */
    public function getEvolutionCause() {
        return $this->evolutionCause;
    }

    /**
     * 
     * @param type $ref
     * @return type
     */
    public function setRef($ref) {
        $this->ref = $ref;
        return $ref;
    }

    /**
     * 
     * @return type
     */
    public function getRef() {
        return $this->ref;
    }

    /**
     * 
     * @param type $action
     * @return type
     */
    public function setAction($action) {
        $this->action = $action;
        return $action;
    }

    /**
     * 
     * @return type
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 
     * @param type $dateStart
     * @return type
     */
    public function setDateStart($dateStart) {
        $this->dateStart = $dateStart;
        return $dateStart;
    }

    /**
     * 
     * @return type
     */
    public function getDateStart() {
        return $this->dateStart;
    }

    /**
     * 
     * @param type $dateEnd
     * @return type
     */
    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;
        return $dateEnd;
    }

    /**
     * 
     * @return type
     */
    public function getDateEnd() {
        return $this->dateEnd;
    }

    /**
     * 
     * @param type $advance
     * @return type
     */
    public function setAdvance($advance) {
        $this->advance = $advance;
        return $advance;
    }

    /**
     * 
     * @return type
     */
    public function getAdvance() {
        return $this->advance;
    }
    

    /**
     * 
     * @param type $observations
     * @return type
     */
    public function setObservations($observations) {
        $this->observations = $observations;
        return $observations;
    }

    /**
     * 
     * @return type
     */
    public function getObservations() {
        return $this->observations;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Indicator
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return IndicatorSimpleValue
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param \Pequiven\SEIPBundle\Entity\User $createdBy
     * @return IndicatorSimpleValue
     */
    public function setCreatedBy(\Pequiven\SEIPBundle\Entity\User $createdBy) {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Pequiven\SEIPBundle\Entity\User 
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return ManagementSystem
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }    

    /**
     * Set indicatorAction
     *
     * @param \Pequiven\SIGBundle\Entity\TypeActionManagementSystem $indicatorAction
     */
    public function setIndicatorAction(\Pequiven\SIGBundle\Entity\TypeActionManagementSystem $indicatorAction) {
        
        $this->indicatorAction = $indicatorAction;

        return $this;
    }

    /**
     * Get indicatorAction
     *
     * @return Pequiven\SIGBundle\Entity\TypeActionManagementSystem
     */
    public function getIndicatorAction() {
        return $this->indicatorAction;
    }
        
    /**
     * Set indicatorRel
     *
     * @param \Pequiven\IndicatorBundle\Entity\Indicator $indicator
     * @return Indicator
     */
    public function setIndicatorRel(\Pequiven\IndicatorBundle\Entity\Indicator $indicatorRel) {
        
        $this->indicatorRel = $indicatorRel;

        return $this;
    }

    /**
     * Get indicatorRel
     *
     * @return Pequiven\IndicatorBundle\Entity\Indicator
     */
    public function getIndicator() {
        return $this->indicatorRel;
    }

    /**
     * Set actionVerification
     *
     * @param \Pequiven\SIGBundle\Entity\TypeVerificationManagementSystem $actionVerification
     */
    public function setActionVerification(\Pequiven\SIGBundle\Entity\TypeVerificationManagementSystem $actionVerification) {
        
        $this->actionVerification = $actionVerification;

        return $this;
    }

    /**
     * Get actionVerification
     *
     * @return Pequiven\SIGBundle\Entity\TypeVerificationManagementSystem
     */
    public function getActionVerification() {
        return $this->actionVerification;
    }
}

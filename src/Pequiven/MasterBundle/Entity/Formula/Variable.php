<?php

namespace Pequiven\MasterBundle\Entity\Formula;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Variable
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Variable implements \Pequiven\SEIPBundle\Entity\PeriodItemInterface
{
    /**
     * Nombre de la variable que se usara para calculos de equacion plan
     */
    const VARIABLE_REAL_AND_PLAN_FROM_EQ_PLAN = 'plan';
    /**
     * Nombre de la variable que se usara para calculos de equacion real
     */
    const VARIABLE_REAL_AND_PLAN_FROM_EQ_REAL = 'real';
    
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

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
     * Formulas que usan esta variable
     * 
     * @var \Pequiven\MasterBundle\Entity\Formula
     * @ORM\ManyToMany(targetEntity="Pequiven\MasterBundle\Entity\Formula", mappedBy="variables")
     */
    protected $formulas;
    
    /**
     * Periodo.
     * 
     * @var \Pequiven\SEIPBundle\Entity\Period
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\Period")
     * @ORM\JoinColumn(nullable=false)
     */
    private $period;
    
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
     * Set description
     *
     * @param string $description
     * @return Variable
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
     * Set name
     *
     * @param string $name
     * @return Variable
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Variable
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
     * @return Variable
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
     * Add formulas
     *
     * @param \Pequiven\MasterBundle\Entity\Formula $formulas
     * @return Variable
     */
    public function addFormula(\Pequiven\MasterBundle\Entity\Formula $formulas)
    {
        $this->formulas[] = $formulas;

        return $this;
    }

    /**
     * Remove formulas
     *
     * @param \Pequiven\MasterBundle\Entity\Formula $formulas
     */
    public function removeFormula(\Pequiven\MasterBundle\Entity\Formula $formulas)
    {
        $this->formulas->removeElement($formulas);
    }

    /**
     * Get formulas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormulas()
    {
        return $this->formulas;
    }
    
    public function __toString()
    {
        return $this->getName()?: '-';
    }
    
    function getPeriod() {
        return $this->period;
    }

    function setPeriod(\Pequiven\SEIPBundle\Entity\Period $period) {
        $this->period = $period;
        
        return $this;
    }
    
    public function __clone() {
        if($this->id > 0){
            $this->id = null;
            $this->createdAt = null;
            $this->updatedAt = null;
            $this->period = null;
        }
    }
}

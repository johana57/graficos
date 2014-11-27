<?php

namespace Pequiven\SEIPBundle\Entity\Result;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pequiven\SEIPBundle\Model\Result\Result as ModelResult;

/**
 * Resultado
 *
 * @author Carlos Mendoza<inhack20@gmail.com>
 * @ORM\Table(name="seip_result")
 * @ORM\Entity(repositoryClass="Pequiven\SEIPBundle\Repository\Result\ResultRepository")
 */
class Result extends ModelResult
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
     * Usuario que creo el resultado
     * 
     * @var \Pequiven\SEIPBundle\Entity\User
     * 
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;
    
    /**
     * Date created
     * 
     * @var type 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at",type="datetime")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;
    
    /**
     * Tipo de resultado
     * 
     * @var integer
     * @ORM\Column(name="typeResult",type="integer",nullable=false)
     */
    private $typeResult;
    
    /**
     * Tipo de calculo
     * 
     * @var integer
     * @ORM\Column(name="typeCalculation",type="integer",nullable=false)
     */
    private $typeCalculation;
    
    /**
     * Resultados asociados
     * 
     * @ORM\OneToMany(targetEntity="\Pequiven\ObjetiveBundle\Entity\Objetive", mappedBy="parent", cascade={"persist"})
     */
    private $childrens;
    
    /**
     * Resultado que impacta
     * 
     * @ORM\ManyToOne(targetEntity="\Pequiven\ObjetiveBundle\Entity\Objetive", inversedBy="childrens")
     */
    private $parent;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Pequiven\ObjetiveBundle\Entity\Objetive", inversedBy="results")
     */
    private $objetives;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childrens = new \Doctrine\Common\Collections\ArrayCollection();
        $this->objetives = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Result
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
     * @return Result
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
     * Set typeResult
     *
     * @param integer $typeResult
     * @return Result
     */
    public function setTypeResult($typeResult)
    {
        $this->typeResult = $typeResult;

        return $this;
    }

    /**
     * Get typeResult
     *
     * @return integer 
     */
    public function getTypeResult()
    {
        return $this->typeResult;
    }

    /**
     * Set typeCalculation
     *
     * @param integer $typeCalculation
     * @return Result
     */
    public function setTypeCalculation($typeCalculation)
    {
        $this->typeCalculation = $typeCalculation;

        return $this;
    }

    /**
     * Get typeCalculation
     *
     * @return integer 
     */
    public function getTypeCalculation()
    {
        return $this->typeCalculation;
    }

    /**
     * Add childrens
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $childrens
     * @return Result
     */
    public function addChildren(\Pequiven\ObjetiveBundle\Entity\Objetive $childrens)
    {
        $this->childrens->add($childrens);

        return $this;
    }

    /**
     * Remove childrens
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $childrens
     */
    public function removeChildren(\Pequiven\ObjetiveBundle\Entity\Objetive $childrens)
    {
        $this->childrens->removeElement($childrens);
    }

    /**
     * Get childrens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * Set parent
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $parent
     * @return Result
     */
    public function setParent(\Pequiven\ObjetiveBundle\Entity\Objetive $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Pequiven\ObjetiveBundle\Entity\Objetive 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add objetives
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $objetives
     * @return Result
     */
    public function addObjetive(\Pequiven\ObjetiveBundle\Entity\Objetive $objetives)
    {
        $this->objetives->add($objetives);

        return $this;
    }

    /**
     * Remove objetives
     *
     * @param \Pequiven\ObjetiveBundle\Entity\Objetive $objetives
     */
    public function removeObjetive(\Pequiven\ObjetiveBundle\Entity\Objetive $objetives)
    {
        $this->objetives->removeElement($objetives);
    }

    /**
     * Get objetives
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObjetives()
    {
        return $this->objetives;
    }

    /**
     * Set createdBy
     *
     * @param \Pequiven\SEIPBundle\Entity\User $createdBy
     * @return Result
     */
    public function setCreatedBy(\Pequiven\SEIPBundle\Entity\User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Pequiven\SEIPBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}

<?php

namespace Pequiven\MasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Pequiven\MasterBundle\Model\Gerencia as modelGerencia;
use Pequiven\MasterBundle\Model\Evaluation\AuditableInterface;
use Pequiven\MasterBundle\Entity\GerenciaSecond;

/**
 * Coordinaciones
 *
 * @ORM\Table(name="seip_c_coordinacion")
 * @ORM\Entity
 * @author Gilbert <glavrjk@gmail.com>
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Coordinacion {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * Descripción
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100)
     */
    protected $description;

    /**
     * Gerencia de 2da Línea
     * @var \Pequiven\MasterBundle\Entity\GerenciaSecond
     * @ORM\ManyToOne(targetEntity="\Pequiven\MasterBundle\Entity\GerenciaSecond",inversedBy="coordinaciones")     
     */
    private $gerenciasecond;

    /**
     * @var string
     *
     * @ORM\Column(name="sumary", type="string", length=100, nullable=true)
     */
    private $sumary;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var \Pequiven\MasterBundle\Entity\FeeStructure
     * @ORM\OneToMany(targetEntity="\Pequiven\MasterBundle\Entity\FeeStructure", mappedBy="coordinacion",cascade={"persist","remove"})
     */
    private $feeStructure;

    public function __construct() {
        $this->feeStructure = new \Doctrine\Common\Collections\ArrayCollection();
    }

    function getId() {
        return $this->id;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function getUpdatedAt() {
        return $this->updatedAt;
    }

    function getDeletedAt() {
        return $this->deletedAt;
    }

    function getDescription() {
        return $this->description;
    }

    function getGerenciaSecond() {
        return $this->gerenciaSecond;
    }

    function getSumary() {
        return $this->sumary;
    }

    function getEnabled() {
        return $this->enabled;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setCreatedAt(\DateTime $createdAt) {
        $this->createdAt = $createdAt;
    }

    function setUpdatedAt(\DateTime $updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    function setDeletedAt($deletedAt) {
        $this->deletedAt = $deletedAt;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setGerenciaSecond(\Pequiven\MasterBundle\Entity\GerenciaSecond $gerenciaSecond) {
        $this->gerenciaSecond = $gerenciaSecond;
    }

    function setSumary($sumary) {
        $this->sumary = $sumary;
    }

    function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

}

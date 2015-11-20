<?php

namespace Pequiven\SEIPBundle\Entity\Sip\Center;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pequiven\SEIPBundle\Model\Sip\Center\Observations as modelObservations;

/**
 * Parroquia
 * @author Maximo Sojo maxsojo13@gmail.com
 * @ORM\Table(name="sip_centro_observations")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Observations extends modelObservations{

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
     * 
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigoCentro", type="integer")
     */
    private $codigoCentro;
    
    /**
     * @var string
     *
     * @ORM\Column(name="observations", type="text")
     */
    private $observations;

    /**
     * @var string
     *
     * @ORM\Column(name="categoria", type="integer")
     */
    private $categoria;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="centro", type="text")
     */
    private $centro;

     /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;
    
    
    function getId() {
        return $this->id;
    }   

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function getFecha() {
        return $this->fecha;
    }

    function setCodigoCentro($codigoCentro) {
        $this->codigoCentro = $codigoCentro;
    }
    
    function getCodigoCentro() {
        return $this->codigoCentro;
    }

    function setObservations($observations) {
        $this->observations = $observations;
    }

    function getObservations() {
        return $this->observations;
    }

    function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    function getCategoria() {
        return $this->categoria;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getStatus() {
        return $this->status;
    }

    function setCentro($centro) {
        $this->centro = $centro;
    }

    function getCentro() {
        return $this->centro;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return material
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

}

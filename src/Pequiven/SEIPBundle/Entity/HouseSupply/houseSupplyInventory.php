<?php

namespace Pequiven\SEIPBundle\Entity\HouseSupply;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pequiven\SEIPBundle\Entity\User;
use Pequiven\SEIPBundle\Entity\HouseSupply\houseSupplyDeposit;
use Pequiven\SEIPBundle\Entity\HouseSupply\houseSupplyProduct;

/**
 * Productos
 *
 * @author Máximo Sojo <maxsojo13@gmail.com>
 * 
 * @ORM\Table(name="seip_gsh_inventory")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class houseSupplyInventory {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Product
     * @var houseSupplyProduct
     * @ORM\ManyToOne(targetEntity="houseSupplyProduct", inversedBy="inventory")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     */
    private $product;

    /**
     * 
     * @var houseSupplyDeposit
     * @ORM\ManyToOne(targetEntity="houseSupplyDeposit", inversedBy="inventory")
     * @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
     */
    private $deposit;

    /**
     *
     * @var float
     * @ORM\Column(name="available",type="float",nullable=false)
     */
    private $available;

    /**
     *
     * @var datetime
     * @ORM\Column(name="lastChargeDate",type="datetime",nullable=false)
     */
    private $lastChargeDate;

    /**
     * Creado por
     * @var \Pequiven\SEIPBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
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
    
    function getId() {
        return $this->id;
    }

    function getProduct() {
        return $this->product;
    }

    function getDeposit() {
        return $this->deposit;
    }

    function getAvailable() {
        return $this->available;
    }

    function getLastChargeDate() {
        return $this->lastChargeDate;
    }

    function getCreatedBy() {
        return $this->createdBy;
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

    function setId($id) {
        $this->id = $id;
    }

    function setProduct(houseSupplyProduct $product) {
        $this->product = $product;
    }

    function setDeposit(houseSupplyDeposit $deposit) {
        $this->deposit = $deposit;
    }

    function setAvailable($available) {
        $this->available = $available;
    }

    function setLastChargeDate($lastChargeDate) {
        $this->lastChargeDate = $lastChargeDate;
    }

    function setCreatedBy(User $createdBy) {
        $this->createdBy = $createdBy;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    function setDeletedAt($deletedAt) {
        $this->deletedAt = $deletedAt;
    }



}

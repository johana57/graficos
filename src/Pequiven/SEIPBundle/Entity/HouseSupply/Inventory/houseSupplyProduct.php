<?php

namespace Pequiven\SEIPBundle\Entity\HouseSupply\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pequiven\SEIPBundle\Entity\User;
use Pequiven\SEIPBundle\Entity\HouseSupply\Inventory\houseSupplyInventory;
use Pequiven\SEIPBundle\Entity\HouseSupply\Inventory\houseSupplyInventoryChargeItems;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Productos
 *
 * @author Máximo Sojo
 * 
 * @ORM\Table(name="seip_gsh_product")
 * @ORM\Entity(repositoryClass="Pequiven\SEIPBundle\Repository\HouseSupply\Inventory\HouseSupplyProductRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class houseSupplyProduct {

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(name="code",type="string",nullable=true)
     */
    private $code;

    /**
     *
     * @var string
     * @ORM\Column(name="description",type="string",nullable=false)
     */
    private $description;

    /**
     *
     * @var float
     * @ORM\Column(name="price",type="float",nullable=false)
     */
    private $price;

    /**
     *
     * @var float
     * @ORM\Column(name="cost",type="float",nullable=true)
     */
    private $cost;

    /**
     *
     * @var float
     * @ORM\Column(name="maxPerUser",type="float",nullable=false)
     */
    private $maxPerUser;

    /**
     * Inventario
     * 
     * @var houseSupplyInventory
     * @ORM\OneToMany(targetEntity="Pequiven\SEIPBundle\Entity\houseSupply\Inventory\houseSupplyInventory",mappedBy="product",cascade={"persist","remove"})
     */
    protected $inventory;

    /**
     * Carga de Items de Inventario
     * 
     * @var houseSupplyInventoryChargeItems
     * @ORM\OneToMany(targetEntity="Pequiven\SEIPBundle\Entity\houseSupply\Inventory\houseSupplyInventoryChargeItems",mappedBy="product",cascade={"persist","remove"})
     */
    protected $inventoryChargeItems;

    /**
     * @var houseSupplyBillingItems
     * @ORM\OneToMany(targetEntity="\Pequiven\SEIPBundle\Entity\HouseSupply\Billing\houseSupplyBillingItems",mappedBy="product",cascade={"persist"}))
     */
    protected $billingItems;

    

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
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

    function getCode() {
        return $this->code;
    }

    function getDescription() {
        return $this->description;
    }

    function getPrice() {
        return $this->price;
    }

    function getCost() {
        return $this->cost;
    }

    function getMaxPerUser() {
        return $this->maxPerUser;
    }

    function getInventory() {
        return $this->inventory;
    }

    function getInventoryChargeItems() {
        return $this->inventoryChargeItems;
    }

    function getBillingItems() {
        return $this->billingItems;
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

    function setCode($code) {
        $this->code = $code;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function setCost($cost) {
        $this->cost = $cost;
    }

    function setMaxPerUser($maxPerUser) {
        $this->maxPerUser = $maxPerUser;
    }

    function setInventory(houseSupplyInventory $inventory) {
        $this->inventory = $inventory;
    }

    function setInventoryChargeItems(houseSupplyInventoryChargeItems $inventoryChargeItems) {
        $this->inventoryChargeItems = $inventoryChargeItems;
    }

    function setBillingItems(houseSupplyBillingItems $billingItems) {
        $this->billingItems = $billingItems;
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

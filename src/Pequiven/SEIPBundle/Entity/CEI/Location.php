<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pequiven\SEIPBundle\Entity\CEI;

use Doctrine\ORM\Mapping as ORM;
use Pequiven\SEIPBundle\Model\CEI\Location as Model;

/**
 * Sede (Control estadistico de informacion)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\Table(name="seip_cei_Location")
 * @ORM\Entity()
 */
class Location extends Model
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
     * Empresa
     * @var Company
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\CEI\Company")
     * @ORM\Joincolumn(nullable=false)
     */
    private $company;
    
    /**
     * Nombre de la sede
     * @var String 
     * @ORM\Column(name="name",type="text",nullable=false)
     */
    private $name;
    
    /**
     * Tipo de sede
     * @var \Pequiven\SEIPBundle\Entity\CEI\TypeLocation
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\CEI\TypeLocation")
     * @ORM\Joincolumn(nullable=false)
     */
    private $typeLocation;

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
     * Set name
     *
     * @param string $name
     * @return Location
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
     * Set company
     *
     * @param \Pequiven\SEIPBundle\Entity\CEI\Company $company
     * @return Location
     */
    public function setCompany(\Pequiven\SEIPBundle\Entity\CEI\Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Pequiven\SEIPBundle\Entity\CEI\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set typeLocation
     *
     * @param \Pequiven\SEIPBundle\Entity\CEI\TypeLocation $typeLocation
     * @return Location
     */
    public function setTypeLocation(\Pequiven\SEIPBundle\Entity\CEI\TypeLocation $typeLocation)
    {
        $this->typeLocation = $typeLocation;

        return $this;
    }

    /**
     * Get typeLocation
     *
     * @return \Pequiven\SEIPBundle\Entity\CEI\TypeLocation 
     */
    public function getTypeLocation()
    {
        return $this->typeLocation;
    }
}

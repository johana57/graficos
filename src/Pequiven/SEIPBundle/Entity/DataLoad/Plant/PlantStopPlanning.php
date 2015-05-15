<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pequiven\SEIPBundle\Entity\DataLoad\Plant;

use Doctrine\ORM\Mapping as ORM;
use Pequiven\MasterBundle\Model\Base\ModelBaseMaster;
/**
 * Planificacion de la planta
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\Table(name="seip_report_plant_stop_planning")
 * @ORM\Entity()
 */
class PlantStopPlanning extends ModelBaseMaster
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
     * Mes
     * @var integer
     * @ORM\Column(name="month",type="integer",nullable=false)
     */
    private $month;
    
    /**
     * Total dias planificadas en el mes
     * @var float
     * @ORM\Column(name="total_stops",type="integer")
     */
    private $totalStops;
    
    /**
     * Total paradas en horas planificadas en el mes
     * @var float
     * @ORM\Column(name="total_hours",type="integer")
     */
    private $totalHours;
    
    /**
     * Dias de paradas
     * @var \Pequiven\SEIPBundle\Entity\DataLoad\Plant\DayStop
     * @ORM\OneToMany(targetEntity="Pequiven\SEIPBundle\Entity\DataLoad\Plant\DayStop",mappedBy="plantStopPlanning",cascade={"persist","remove"})
     */
    private $dayStops;
    
    /**
     * Reporte de planta
     * @var \Pequiven\SEIPBundle\Entity\DataLoad\PlantReport
     * @ORM\ManyToOne(targetEntity="Pequiven\SEIPBundle\Entity\DataLoad\PlantReport",inversedBy="plantStopPlannings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plantReport;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dayStops = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set month
     *
     * @param integer $month
     * @return PlantStopPlanning
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set totalStops
     *
     * @param integer $totalStops
     * @return PlantStopPlanning
     */
    public function setTotalStops($totalStops)
    {
        $this->totalStops = $totalStops;

        return $this;
    }

    /**
     * Get totalStops
     *
     * @return integer 
     */
    public function getTotalStops()
    {
        return $this->totalStops;
    }

    /**
     * Set totalHours
     *
     * @param integer $totalHours
     * @return PlantStopPlanning
     */
    public function setTotalHours($totalHours)
    {
        $this->totalHours = $totalHours;

        return $this;
    }

    /**
     * Get totalHours
     *
     * @return integer 
     */
    public function getTotalHours()
    {
        return $this->totalHours;
    }

    /**
     * Add dayStops
     *
     * @param \Pequiven\SEIPBundle\Entity\DataLoad\Plant\DayStop $dayStops
     * @return PlantStopPlanning
     */
    public function addDayStop(\Pequiven\SEIPBundle\Entity\DataLoad\Plant\DayStop $dayStops)
    {
        $dayStops->setPlantStopPlanning($this);
        $this->dayStops->add($dayStops);

        return $this;
    }

    /**
     * Remove dayStops
     *
     * @param \Pequiven\SEIPBundle\Entity\DataLoad\Plant\DayStop $dayStops
     */
    public function removeDayStop(\Pequiven\SEIPBundle\Entity\DataLoad\Plant\DayStop $dayStops)
    {
        $this->dayStops->removeElement($dayStops);
    }

    /**
     * Get dayStops
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDayStops()
    {
        return $this->dayStops;
    }

    /**
     * Set plantReport
     *
     * @param \Pequiven\SEIPBundle\Entity\DataLoad\PlantReport $plantReport
     * @return PlantStopPlanning
     */
    public function setPlantReport(\Pequiven\SEIPBundle\Entity\DataLoad\PlantReport $plantReport)
    {
        $this->plantReport = $plantReport;

        return $this;
    }

    /**
     * Get plantReport
     *
     * @return \Pequiven\SEIPBundle\Entity\DataLoad\PlantReport 
     */
    public function getPlantReport()
    {
        return $this->plantReport;
    }
}

<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pequiven\SEIPBundle\Model\DataLoad\RawMaterial;

use Pequiven\SEIPBundle\Model\BaseModel;

/**
 * Modelo de materia prima
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class RawMaterialConsumptionPlanning extends BaseModel
{
    /**
     * Tipo de materia prima interna
     */
    const TYPE_INTERNAL = 0;
    
    /**
     * Tipo de materia prima externa
     */
    const TYPE_EXTERNAL = 1;
    
    public static function getTypeLabels()
    {
        return array(
            self::TYPE_INTERNAL => "pequiven_seip.raw_material.type.internal",
            self::TYPE_EXTERNAL => "pequiven_seip.raw_material.type.external",
        );
    }
    
    public function getTypeLabel()
    {
        $typeLabels = $this->getTypeLabels();
        $type = $this->getType();
        $label = "";
        if(isset($typeLabels[$type])){
            $label = $typeLabels[$type];
        }
        return $label;
    }
    
    function getDetailByMonth()
    {
        $detailRawMaterialConsumptions = $this->getDetailRawMaterialConsumptions();
        $result = array();
        foreach ($detailRawMaterialConsumptions as $detailRawMaterialConsumption) {
            $result[$detailRawMaterialConsumption->getMonth()] = $detailRawMaterialConsumption;
        }
        ksort($result);
        return $result;
    }
    
    /**
     * Retorna un resumen
     * @param \DateTime $date
     * @return type
     */
    public function getSummary(\DateTime $date)
    {
        $month = (int)$date->format("m");
        $day = (int)$date->format("d");
        
        $totalDay = $totalMonth = $totalYear = 0.0;
        $details = $this->getDetailByMonth();
        foreach ($details as $monthDetail => $detail) {
                $totalYear = $totalYear + $detail->getTotalReal();
                
                if($monthDetail > $month){
                    break;
                }

                if($month == $monthDetail){
                    $totalDayName = 'getDay'.$day.'Real';
                    $totalDay = $detail->$totalDayName();
                    $totalMonth = $totalMonth + $detail->getTotalReal();
                }
        }
        
        $total = array(
            'total_day' => $totalDay,
            'total_month' => $totalMonth,
            'total_year' => $totalYear,
        );
        return $total;
    }
}

<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pequiven\SEIPBundle\Controller\DataLoad;

use Pequiven\SEIPBundle\Controller\SEIPController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador de producto de reporte
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ProductReportController extends SEIPController
{
    public function createNew() {
        $entity = parent::createNew();
        $request = $this->getRequest();
        $plantReportId = $request->get("plantReport");
        if($plantReportId > 0){
            $em = $this->getDoctrine()->getManager();
            $plantReport = $em->find("Pequiven\SEIPBundle\Entity\DataLoad\PlantReport", $plantReportId);
            $entity->setPlantReport($plantReport);
        }
        return $entity;
    }
    
    public function runPlanningAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $productPlannings = $resource->getProductPlannings();
        $plantStopPlanningsByMonths = $resource->getPlantReport()->getPlantStopPlanningSortByMonth();
        
        $propertyAccessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
        
        $countProduct = 0;
        $productDetailDailyMonthsCache = $resource->getProductDetailDailyMonthsSortByMonth();
        foreach ($productPlannings as $productPlanning) {
            $daysStopsArray = array();
            $month = $productPlanning->getMonth();
            if(isset($plantStopPlanningsByMonths[$month])){
                $daysStops = $plantStopPlanningsByMonths[$month];
                foreach ($daysStops as $daysStop) {
                    $daysStopsArray[] = $daysStop->getNroDay();
                }
            }
            $ranges = $productPlanning->getRanges();
            
            if(!isset($productDetailDailyMonthsCache[$productPlanning->getMonth()])){
                $productDetailDailyMonth = new \Pequiven\SEIPBundle\Entity\DataLoad\Production\ProductDetailDailyMonth();
                $productDetailDailyMonth->setMonth($productPlanning->getMonth());
                $productDetailDailyMonth->setProductReport($resource);
                $productDetailDailyMonthsCache[$productPlanning->getMonth()] = $productDetailDailyMonth;
            }else{
                $productDetailDailyMonth = $productDetailDailyMonthsCache[$productPlanning->getMonth()];
            }
            
            $type = $productPlanning->getType();
            $prefix = "";
            if($type === \Pequiven\SEIPBundle\Model\DataLoad\Production\ProductPlanning::TYPE_GROSS){
                $prefix = "Gross";
            }else if($type === \Pequiven\SEIPBundle\Model\DataLoad\Production\ProductPlanning::TYPE_NET){
                $prefix = "Net";
            }
            
            foreach ($ranges as $range) {
                $dateFrom = $range->getDateFrom();
                $dateEnd = $range->getDateEnd();
                
                $dayFrom = $dateFrom->format("d");
                $dayEnd = $dateEnd->format("d");
                $type = $range->getType();
                $originalValue = $range->getValue();
                $value = 0;
                
                if($type === \Pequiven\SEIPBundle\Model\DataLoad\Production\Range::TYPE_FIXED_VALUE){
                    $value = $originalValue;
                }else if($type === \Pequiven\SEIPBundle\Model\DataLoad\Production\Range::TYPE_CAPACITY_FACTOR){
                    $productPlanning = $range->getProductPlanning();
                    $value = ($productPlanning->getDailyProductionCapacity() / 100) * $originalValue;
                }
                
                for($day = $dayFrom; $day < $dayEnd; $day++){
                    $dayInt = (int)$day;
                    $propertyPath = sprintf("day%s%sPlan",$dayInt,$prefix);
                    if(in_array($dayInt,$daysStopsArray)){
                        $propertyAccessor->setValue($productDetailDailyMonth, $propertyPath, 0);
                        continue;
                    }
                    $propertyAccessor->setValue($productDetailDailyMonth, $propertyPath, $value);
                }
            }
            $countProduct++;
        }
        if($countProduct > 0){
            foreach ($productDetailDailyMonthsCache as $productDetailDailyMonth) {
                $this->save($productDetailDailyMonth,false);
            }
            $this->flush();
        }
        $months = \Pequiven\SEIPBundle\Service\ToolService::getMonthsLabels();
        $inventorys = $resource->getInventorySortByMonth();
        foreach ($months as $month => $label) {
            if(!isset($inventorys[$month])){
                $inventory = new \Pequiven\SEIPBundle\Entity\DataLoad\Inventory\Inventory();
                $inventory->setMonth($month);
                $resource->addInventory($inventory);
                
                $this->save($inventory,false);
            }
        }
        $this->flush();
        
        return $this->redirectHandler->redirectTo($resource);
    }
    
    public function deleteAction(Request $request) 
    {
        $resource = $this->findOr404($request);
        
        $url = $this->generateUrl("pequiven_report_template_show",array(
            "id" => $resource->getReportTemplate()->getId(),
        ));
        
        $this->domainManager->delete($resource);
        return $this->redirect($url);
    }
}

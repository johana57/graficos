<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pequiven\SEIPBundle\EventListener;

/**
 * Description of EntitySubscriber
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class EntitySubscriber extends BaseEventListerner
{
    public static function getSubscribedEvents() {
        return array(
            SeipEvents::REPORT_TEMPLATE_PRE_CREATE => "onReportTemplatePreCreate",
            SeipEvents::PRODUCT_REPORT_PRE_CREATE => "onProductReportPreCreate",
            SeipEvents::PRODUCT_PLANNING_PRE_CREATE => "onProductPlanningPreCreate",
            SeipEvents::PRODUCT_RANGE_CREATE => "onProductRangePreCreate",
        );
    }
    
    public function onReportTemplatePreCreate(\Sylius\Bundle\ResourceBundle\Event\ResourceEvent $event)
    {
        $entity = $event->getSubject();
        
        $entity->setPeriod($this->getPeriodService()->getPeriodActive(true));
        $entity->setRef($this->getSequenceGenerator()->getNextRefReportTemplate($entity));
        
    }
    
    public function onProductReportPreCreate(\Sylius\Bundle\ResourceBundle\Event\ResourceEvent $event)
    {
        $entity = $event->getSubject();
        $request = $this->getRequest();
        $reportTemplateId = $request->get("reportTemplate");
        $reportTemplate = $this->find("Pequiven\SEIPBundle\Entity\DataLoad\ReportTemplate", $reportTemplateId);
        $entity->setReportTemplate($reportTemplate);
    }
    
    public function onProductPlanningPreCreate(\Sylius\Bundle\ResourceBundle\Event\ResourceEvent $event)
    {
        $entity = $event->getSubject();
        $request = $this->getRequest();
        $productReportId = $request->get("productReport");
        $productReport = $this->find("Pequiven\SEIPBundle\Entity\DataLoad\ProductReport", $productReportId);
        $entity->setProductReport($productReport);
    }
    
    public function onProductRangePreCreate(\Sylius\Bundle\ResourceBundle\Event\ResourceEvent $event)
    {
        $entity = $event->getSubject();
        $request = $this->getRequest();
        $productPlanningId = $request->get("productPlanning");
        $productPlanning = $this->find("Pequiven\SEIPBundle\Entity\DataLoad\Production\ProductPlanning", $productPlanningId);
        $entity->setProductPlanning($productPlanning);
    }
}

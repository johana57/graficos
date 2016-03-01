<?php

namespace Pequiven\SIGBundle\Controller\SigMonitoring;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tecnocreaciones\Bundle\ResourceBundle\Controller\ResourceController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Pequiven\SIGBundle\Entity\Tracing\Maintenance;
use Pequiven\SIGBundle\Entity\Tracing\MaintenanceAdvance;
use Pequiven\SIGBundle\Form\Tracing\MaintenanceType;
use Pequiven\SIGBundle\Form\Tracing\MaintenanceAdvanceType;
use Pequiven\SIGBundle\Entity\Tracing\Standardization;

/**
 * Controlador Seguimiento y Eficacia (Mantenimiento)
 *
 * @author Maximo Sojo <maxsojo13@gmail.com>
 */
class MaintenanceController extends ResourceController
{
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();                        
        
        $id = $request->get('id');  
        $valueCharge = $advance = 0;

        $period = $this->getPeriodService()->getPeriodActive();

        $standardization = $em->getRepository("\Pequiven\SIGBundle\Entity\Tracing\Standardization")->find($id);
        
        foreach ($standardization->getMaintenance() as $valueMaintenance) {            
            $idMaintenance = $valueMaintenance->getId();            
            $advance = $em->getRepository("\Pequiven\SIGBundle\Entity\Tracing\MaintenanceAdvance")->findBy(array('maintenance' => $idMaintenance));                                        
        }

        $maintenance = new maintenance();
        $maintenanceAdvance = new maintenanceAdvance();

        $form  = $this->createForm(new MaintenanceType($period), $maintenance);
        $formAdvance  = $this->createForm(new MaintenanceAdvanceType($period), $maintenanceAdvance);

        if ($advance) {
            $valueCharge = 1;
        }
        
        if ($request->isMethod('POST')) {            
            $form->handleRequest($request);             
            if ($valueCharge == 0 ) {
                $maintenance->addStandardization($standardization);            
                $em->persist($maintenance);            
                $em->flush();                
                $idMaintenance = $maintenance->getId();
            }
            
            $maintenanceData = $em->getRepository("\Pequiven\SIGBundle\Entity\Tracing\Maintenance")->findOneById($idMaintenance);                        
            $maintenanceAdvance->setMaintenance($maintenanceData);
            $maintenanceAdvance->setAdvance($request->get('sig_maintenance_advance')['advance']);
            $maintenanceAdvance->setAnalysis($request->get('sig_maintenance_advance')['analysis']);
            $maintenanceAdvance->setObservations($request->get('sig_maintenance_advance')['observations']);

            $em->persist($maintenanceAdvance);            
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Datos Cargados Exitosamente");
            die();
        }    

        foreach (Standardization::getDetectionArray() as $key => $value) {
                $labelsDetection[] = array(
                    'id' => $key,
                    'description' => $this->trans($value, array(), 'PequivenSIGBundle'),
                );
            }

        foreach (Standardization::getTypeNcArray() as $key => $value) {
                $labelsTypeNc[] = array(
                    'id' => $key,
                    'description' => $this->trans($value, array(), 'PequivenSIGBundle'),
                );
            }

        $standardization = $em->getRepository("\Pequiven\SIGBundle\Entity\Tracing\Standardization")->find($id);

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('Tracing/Form/form_maintenance.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(                
                'form' => $form->createView(),
                'formAdvance' => $formAdvance->createView(),
                'period_valid'        => '2016',
                'standardization'     => $standardization,
                'labelsDetection'     => $labelsDetection,
                'labelsTypeNc'        => $labelsTypeNc,
                'valueCharge'         => $valueCharge
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    public function showAction(Request $request){
        $em = $this->getDoctrine()->getManager(); 
        $id = $request->get('id');  
        $period = $this->getPeriodService()->getPeriodActive();
        
        $standardization = $em->getRepository("\Pequiven\SIGBundle\Entity\Tracing\Standardization")->find($id);
        foreach ($standardization->getMaintenance() as  $value) {
            $idMaintenance = $value->getId();
        }

        
        $advance = $em->getRepository("\Pequiven\SIGBundle\Entity\Tracing\MaintenanceAdvance")->findBy(array('maintenance' => $idMaintenance));                                        
        
        foreach (Standardization::getDetectionArray() as $key => $value) {
                $labelsDetection[] = array(
                    'id' => $key,
                    'description' => $this->trans($value, array(), 'PequivenSIGBundle'),
                );
            }

        foreach (Standardization::getTypeNcArray() as $key => $value) {
                $labelsTypeNc[] = array(
                    'id' => $key,
                    'description' => $this->trans($value, array(), 'PequivenSIGBundle'),
                );
            }
        
        $statusMaintanence = [
            0 => "Abierta No Vencia",
            1 => "Cerrada",
            2 => "Abierta Vencida"            
        ];

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('Tracing/Form/formShow.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(                              
                'period_valid'        => '2016',
                'standardization'     => $standardization,
                'labelsDetection'     => $labelsDetection,
                'labelsTypeNc'        => $labelsTypeNc,
                'statusMaintanence'   => $statusMaintanence,
                'advance'             => $advance
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     *  Notification
     *
     */
    protected function getNotificationService() {        
        return $this->container->get('seip.service.notification');
    }

    /**
     *  Period
     *
     */
    protected function getPeriodService() {
        return $this->container->get('pequiven_seip.service.period');
    }
}

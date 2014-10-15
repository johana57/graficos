<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pequiven\SEIPBundle\EventListener;

use DateTime;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Pequiven\ArrangementProgramBundle\Entity\GoalDetails;
use Pequiven\ObjetiveBundle\Entity\ObjetiveLevel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of SerializerListener
 *
 * @author matias
 */
class SerializerListener implements EventSubscriberInterface,  ContainerAwareInterface {
    /**
     *
     * @var ContainerInterface
     */
    private $container;
    
    public static function getSubscribedEvents() {
        return array(
            array('event' => Events::POST_SERIALIZE, 'method' => 'onPostSerializeObjetive', 'class' => 'Pequiven\ObjetiveBundle\Entity\Objetive', 'format' => 'json'),
            array('event' => Events::POST_SERIALIZE, 'method' => 'onPostSerializeIndicator', 'class' => 'Pequiven\IndicatorBundle\Entity\Indicator', 'format' => 'json'),
            array('event' => Events::POST_SERIALIZE, 'method' => 'onPostSerializeGoalDetails', 'class' => 'Pequiven\ArrangementProgramBundle\Entity\GoalDetails', 'format' => 'json'),
            array('event' => Events::POST_SERIALIZE, 'method' => 'onPostSerializeArrangementProgram', 'class' => 'Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram', 'format' => 'json'),
        );
    }

    public function onPreSerialize(PreSerializeEvent $event) {
        // do something
    }

    /**
     * Función que serializa el objeto de objetivo para la vista
     * @param ObjectEvent $event
     */
    public function onPostSerializeObjetive(ObjectEvent $event) {
        if ($event->getObject()->getObjetiveLevel() && $event->getObject()->getObjetiveLevel()->getLevel() === ObjetiveLevel::LEVEL_ESTRATEGICO) {
            $lineStrategics = $event->getObject()->getLineStrategics();
            $event->getVisitor()->addData('groupBy', $lineStrategics[0]->getRef() . $lineStrategics[0]->getDescription());
        } elseif ($event->getObject()->getObjetiveLevel() && $event->getObject()->getObjetiveLevel()->getLevel() === ObjetiveLevel::LEVEL_TACTICO) {
            $object = $event->getObject();
            $parents = $object->getParents();
            $valueGroupBy = '';
            foreach ($parents as $parent) {
                $valueGroupBy.= $parent->getRef() . $parent->getDescription();
            }
            $event->getVisitor()->addData('groupBy', $valueGroupBy);
            $event->getVisitor()->addData('totalParents', count($parents));
            $event->getContext();
        } elseif ($event->getObject()->getObjetiveLevel() && $event->getObject()->getObjetiveLevel()->getLevel() === ObjetiveLevel::LEVEL_OPERATIVO) {
            $object = $event->getObject();
            $parents = $object->getParents();
            $valueGroupBy = '';
            foreach ($parents as $parent) {
                $valueGroupBy.= $parent->getRef() . $parent->getDescription();
            }
            $event->getVisitor()->addData('groupBy', $valueGroupBy);
            $event->getVisitor()->addData('totalParents', count($parents));
        }
    }

    /**
     * Función que serializa el objeto de indicador para la vista
     * @param ObjectEvent $event
     */
    public function onPostSerializeIndicator(ObjectEvent $event) {
        $objetives = $event->getObject()->getObjetives();
        $event->getVisitor()->addData('groupBy', $objetives[0]->getRef() . $objetives[0]->getDescription());
    }
    
    public function onPostSerializeGoalDetails(ObjectEvent $event) {
        $data = array();
        $object = $event->getObject();
        
        $date = new DateTime();
        
        //Habilitar la carga de lo real
        $isLoadRealEnabled = true;
        //Habilitar la carga de los planeado
        $isLoadPlannedEnabled = true;
        //Habilitar carga de valores reales de meses adelantados
        $isEnabledLoadRealFuture = false;
        //Habilitar la carga de valores reales atrasados
        $isEnabledLoadRealLate = true;
        //Habilitar edicion del valor real dependiendo si la planeada no esta vacia
        $isEnabledEditByPlannedLoad = true;
        //Deshabilitar las celdas planeadas cuando se distribuya el 100%
        $disablePlannedOnComplete = true;
        
        $month = $date->format('m');
        
        if($isLoadRealEnabled === false){
            foreach (GoalDetails::getMonthsReal() as $key => $monthGoal) {
                $data[$key]['isEnabled'] = false;
            }
        }
        if($isEnabledLoadRealFuture === false){
            foreach (GoalDetails::getMonthsReal() as $key => $monthGoal) {
                if($month < $monthGoal){
                    $data[$key]['isEnabled'] = false;
                }
            }
        }
        if($isLoadPlannedEnabled === false){
            foreach (GoalDetails::getMonthsPlanned() as $key => $monthGoal) {
                $data[$key]['isEnabled'] = false;
            }
        }
        if($isEnabledLoadRealLate === false){
            foreach (GoalDetails::getMonthsReal() as $key => $monthGoal) {
                if($month > $monthGoal){
                    $data[$key]['isEnabled'] = false;
                }
            }
        }
        if($isEnabledEditByPlannedLoad === true){
            $propertyAccessor = new \Symfony\Component\PropertyAccess\PropertyAccessor();
            foreach (GoalDetails::getMonthsPlanned() as $planned => $monthNumber) {
                $value = $propertyAccessor->getValue($object,$planned);
                $monthReal = GoalDetails::getMonthOfRealByMonth($monthNumber);
                if($value == '' || $value == '0' || $value === null){
                    $data[$monthReal]['isEnabled'] = false;
                }
            }
        }
        
        if($disablePlannedOnComplete === true){
            //Limite de porcentaje que se asigna al planeado
            $limitPlannedPercentaje = 100;
            $percentajeAcumulated = 0;
            $propertyAccessor = new \Symfony\Component\PropertyAccess\PropertyAccessor();
            $disable = false;
            foreach (GoalDetails::getMonthsPlanned() as $planned => $monthNumber) {
                $percentaje = $propertyAccessor->getValue($object,$planned);
                $percentajeAcumulated += $percentaje;
                if($disable){
                    $data[$planned]['isEnabled'] = false;
                    continue;
                }
                if($percentajeAcumulated >= $limitPlannedPercentaje){
                    $disable = true;
                }
            }
            foreach (GoalDetails::getMonthsPlanned() as $planned => $monthNumber) {
                $percentaje = $propertyAccessor->getValue($object,$planned);
                if($disable === true && ($percentaje == 0 && $percentaje == '0' || $percentaje == '' || $percentaje == null)){
                    $data[$planned]['isEnabled'] = false;
                }
            }
        }
        $event->getVisitor()->addData('_data',$data);
        
    }
    
    public function onPostSerializeArrangementProgram(ObjectEvent $event) {
        $data = array();
        $object = $event->getObject();
        if($object->getId() > 0){
            $data['self']['href'] = $this->generateUrl('pequiven_seip_arrangementprogram_show', array('id' => $object->getId()));
            $data['self']['edit']['href'] = $this->generateUrl('arrangementprogram_edit', array('id' => $object->getId()));
        }
        $event->getVisitor()->addData('_links',$data);
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    protected function generateUrl($route,array $parameters){
        return $this->container->get('fos_rest.router')->generate($route, $parameters, \Symfony\Bundle\FrameworkBundle\Routing\Router::ABSOLUTE_URL);
    }
    
    function trans($id, $parameters = array(), $domain = 'messages')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
}

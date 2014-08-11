<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pequiven\IndicatorBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pequiven\IndicatorBundle\Entity\Indicator;
use Pequiven\IndicatorBundle\Entity\IndicatorLevel;
use Pequiven\IndicatorBundle\Form\Type\Strategic\RegistrationFormType as BaseFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Description of IndicatorStrategicController
 *
 * @author matias
 */
class IndicatorStrategicController extends Controller {
    //put your code here
    
    public function createFromObjetiveAction(Request $request){

        $form = $this->createForm(new BaseFormType());
        $form->handleRequest($request);
        $nameObject = 'object';
        
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        $em->getConnection()->beginTransaction();
        
        if($form->isValid()){
            $object = $form->getData();
            $data =  $this->container->get('request')->get("pequiven_indicator_strategicfo_registration");
            
            $object->setGoal(bcadd(str_replace(',', '.', $data['weight']),'0',3));
            $object->setGoal(bcadd(str_replace(',', '.', $data['goal']),'0',3));
            $object->setRankTop(bcadd(str_replace(',', '.', $data['rankTop']),'0',3));
            $object->setRankMiddleTop(bcadd(str_replace(',', '.', $data['rankMiddleTop']),'0',3));
            $object->setRankMiddleBottom(bcadd(str_replace(',', '.', $data['rankMiddleBottom']),'0',3));
            $object->setRankBottom(bcadd(str_replace(',', '.', $data['rankBottom']),'0',3));
            $object->setUserCreatedAt($user);
            
            //Obtenemos y seteamos el nivel del indicador
            $indicatorLevel = $em->getRepository('PequivenIndicatorBundle:IndicatorLevel')->findOneBy(array('level' => IndicatorLevel::LEVEL_ESTRATEGICO));
            $object->setIndicatorLevel($indicatorLevel);
            
            //En caso de que el Indicador tenga Fórmula se obtiene y se setea respectivamente
            if(isset($data['formula'])){
                $formula = $em->getRepository('PequivenMasterBundle:Formula')->findOneBy(array('id' => $data['formula']));
                $object->setFormula($formula);
            }
            
            $em->persist($object);
            
            try{
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e){
                $em->getConnection()->rollback();
                throw $e;
            }
            
            return $this->redirect($this->generateUrl('pequiven_indicator_register_redirect'));
        }
        
        return $this->container->get('templating')->renderResponse('PequivenIndicatorBundle:Strategic:registerFromObjetive.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView(),
                ));
    }
    
    /**
     * Devuelve los Indicadores Estratégicos de acuerdo a la Línea Estratégica que este
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function selectIndicatorStrategicFromLineStrategicAction(Request $request){
        $response = new JsonResponse();
        
        $indicatorsStrategic = array();
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        $lineStrategicId = $request->request->get('lineStrategicId');
        $indicatorLevelId = IndicatorLevel::LEVEL_ESTRATEGICO;
        
        $results = $em->getRepository('PequivenIndicatorBundle:Indicator')->findBy(array('lineStrategic' => $lineStrategicId, 'indicatorLevel' => $indicatorLevelId));
        $totalResults = count($results);
        
        if(is_array($results) && $totalResults > 0){
            foreach ($results as $result){
                $indicatorsStrategic[] = array("id" => $result->getId(), "description" => $result->getRef() . ' ' . $result->getDescription());
            }
        } else{
            $indicatorsStrategic[] = array("empty" => true);
        }
        
        $response->setData($indicatorsStrategic);
        
        return $response;
    }
    
    /**
     * Devuelve la Referencia del Indicador, de acuerdo a la cantidad que ya se encuentren registrados por línea estratégica
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function displayRefIndicatorAction(Request $request){
        $response = new JsonResponse();
        $indicator = new Indicator();
        $data = array();
        $options = array();
        
        $lineStrategicId = $request->request->get('lineStrategicId');
        $options['lineStrategicId'] = $lineStrategicId;
        $options['type'] = 'STRATEGIC';
        $ref = $indicator->setNewRef($options);
        
        $data[] = array('ref' => $ref);
        $response->setData($data);
        return $response;
    }
}

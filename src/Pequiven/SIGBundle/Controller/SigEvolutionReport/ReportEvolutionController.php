<?php

namespace Pequiven\SIGBundle\Controller\SigEvolutionReport;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tecnocreaciones\Bundle\ResourceBundle\Controller\ResourceController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Pequiven\IndicatorBundle\Entity\Indicator;

use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCause;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionCauseType;

use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionTrend;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionTrendType;

#Acciones y Valores
use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionAction;
use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionActionValue;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionActionType;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionActionValueType;

#Verification
use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionActionVerification;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionActionVerificationType;

/**
 * Controlador de los distintos modulos del infome de evolucion
 *
 * @author Maximo Sojo <maxsojo13@gmail.com>
 */

class ReportEvolutionController extends ResourceController
{   
    
    /**
     * Retorna el formulario del analisis de la tendencia
     * 
     * @param Request $request
     * @return type
     */
    function getFormTrendAction(Request $request)
    {
        
        $id = $request->get('idIndicator');

        $typeObject = $request->get('typeObj');
        if ($typeObject == 1) {            
            $result = $this->findIndicatorOr404($request);                    
        }elseif($typeObject == 2){            
            $repository = $this->get('pequiven_seip.repository.arrangementprogram');
            $result = $repository->find($id); 
        }
        
        $trend = new EvolutionTrend();
        $form  = $this->createForm(new EvolutionTrendType(), $trend);
        
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('form/form_trend.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'indicator' => $result,
                'form'      => $form->createView(),                
                'period'        => $result->getPeriod()->getName()
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     * Añade la tendencia 
     * 
     * @param Request $request
     * @return type
     */
    public function addTrendAction(Request $request)
    {   
        $result = $request->get('idIndicator');

        $typeObject = $request->get('typeObj');
        
        $month = $request->get('set_data')['month'];//Carga de Mes pasado
        
        $user = $this->getUser();
        
        $trend = new EvolutionTrend();
        $form  = $this->createForm(new EvolutionTrendType(), $trend);
        
        if ($typeObject == 1) {

            $repository = $this->get('pequiven.repository.sig_indicator');
            $results = $repository->find($result);            
            
            $trend->setIndicator($results);

        }elseif ($typeObject == 2) {

            $repository = $this->get('pequiven_seip.repository.arrangementprogram');
            $results = $repository->find($result);            
            
            $trend->setArrangementProgram($results);            
        }        
        
        $trend->setCreatedBy($user);
        $trend->setMonth($month);
        $trend->setTypeObject($typeObject);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($trend);
            $em->flush(); 

            $this->get('session')->getFlashBag()->add('success', "Analisis de Tendencia Añadido Exitosamente");                        
        }     
    }

    /**
     * Elimina el analisis de la tendencia
     * 
     * @param Request $request
     * @return type
     */
    public function deletetrendAction(Request $request)
    {   
        $id = $request->get('id');
        
        $em = $this->getDoctrine()->getManager();
        $results = $this->get('pequiven.repository.sig_trend_report_evolution')->find($id);
        
        if($results){

            $em->remove($results);
            $em->flush();            

            $this->get('session')->getFlashBag()->add('success', $this->trans('flashes.messages.deleteTrend', array(), 'PequivenSIGBundle'));
            return true;
        }  
    }

    /**
     * Retorna el formulario del plan de acción
     * 
     * @param Request $request
     * @return type
     */
    function getFormPlanAction(Request $request)
    {
        $id = $request->get('idIndicator');

        $typeObject = $request->get('typeObj');//Tipo de objeto Indicador o PG

        if ($typeObject == 1) {            
            $result = $this->findIndicatorOr404($request);             
        }elseif($typeObject == 2){            
            $repository = $this->get('pequiven_seip.repository.arrangementprogram');
            $result = $repository->find($id);             
        }
        
        $user = $this->getUser();//Carga de usuario
        $data = $this->findEvolutionCause($request);//Carga la data de las causas y sus acciones relacionadas
        
        $id = $result->getId();

        $config = [
            'id' => 'form_action_evolution'
        ];      
        
        $cause = new EvolutionAction();
        $form  = $this->createForm(new EvolutionActionType($id, $typeObject));
        $form_value  = $this->createForm(new EvolutionActionValueType());
        
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('form/form_action.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'indicator'     => $result,
                'config'        => $config,                
                'form_value'    => $form_value->createView(),
                'form'          => $form->createView(),
                'period'        => $result->getPeriod()->getName()
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     * Retorna el formulario del plan de acción para cargar valores
     * 
     * @param Request $request
     * @return type
     */
    function getFormPlanAddAction(Request $request)
    {   
        $id = $request->get('idIndicator');

        $typeObject = $request->get('typeObj');

        if ($typeObject == 1) {            
            $result = $this->findIndicatorOr404($request);                    
        }elseif($typeObject == 2){            
            $repository = $this->get('pequiven_seip.repository.arrangementprogram');
            $result = $repository->find($id); 
        }

        $user = $this->getUser();//Carga de usuario
        $data = $this->findEvolutionCause($request);//Carga la data de las causas y sus acciones relacionadas

        $form_value  = $this->createForm(new EvolutionActionValueType());
        $form = 0;
        
        $config = [
            'id' => 'form_action_values_evolution'
        ];
        
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('form/form_action.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'indicator'     => $result,                
                'config'        => $config,
                'form_value'    => $form_value->createView(),
                'form'          => $form,
                'period'        => $result->getPeriod()->getName()                
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     * Añade el Plan de Acción
     * 
     * @param Request $request
     * @return type
     */
    public function addAction(Request $request)
    {   
        //$month = date("m");//Carga del mes de Creación de la causa "Automatico"
        $indicator = $this->findIndicatorOr404($request);           
        $em = $this->getDoctrine()->getManager();

        $id = $this->getRequest()->get('idIndicator');
        $typeObject = $this->getRequest()->get('typeObj');
        
        $user = $this->getUser();
        
        $monthSet    = $request->get('set_data')['month'];//recibiendo mes
        $causeAction = $request->get('actionResults')['evolutionCause'];//Recibiendo Causa
        $responsible = $request->get('actionResults')['responsible'];//Recibiendo Responsable
        $AcValue     = $request->get('actionValue')['advance'];//RecibiendoValue
        
        $ref = $this->findEvolutionActionRef($request, $monthSet, $causeAction); //Carga la data de la referencia

        $AcObservation = $request->get('actionValue')['observations'];//RecibiendoObservations

        $causeResult = $this->get('pequiven.repository.sig_causes_report_evolution')->find($causeAction);        
        
        //Calculando la cantidad de meses que durara la acción
        $dateStart = $request->get('actionResults')['dateStart'];
        $dateEnd   = $request->get('actionResults')['dateEnd'];                
        
        $monthStart = explode("/", $dateStart);//Sacando el mes de inicio
        $monthEnd   = explode("/", $dateEnd);//Sacando el mes de cierre        
        
        $dStart = $monthStart[1];//Pasando mes de Inicio
        $dEnd   = $monthEnd[1];//Pasando el mes de Cierre
        
        $count = 0; $data = (int)$dStart;
            
        $em->getConnection()->beginTransaction();
        $action = new EvolutionAction();
        $form  = $this->createForm(new EvolutionActionType($id, $typeObject), $action);
        
        $form->handleRequest($request);
        
        $contaRes = 0;   
        //Recibiendo responsables
        $reponsibles = explode(",", $responsible);         
        $catnRes = count($reponsibles);
        //Creación de url
        $routeParameters = array(                
            'id'    => $id,
            'month' => $monthSet
        );

        $apiDataUrl = $this->generateUrl('pequiven_indicator_evolution', $routeParameters);
        $apiDataUrl = "http://".$_SERVER['HTTP_HOST'].$apiDataUrl;

        //Añadiendo responsables
        for ($i=0; $i < $catnRes; $i++) { 
            $user = $this->get('pequiven_seip.repository.user')->find($reponsibles[$i]);
            $notification = $this->getNotificationService()->setDataNotification("Informe de Evolución", "Ha sido asignado como responsable a un Plan de Acción en el Informe de Evolucion del Indicador ". $indicator->getRef() ." con fecha de incio: ".$dateStart." y fecha de cierre: ".$dateEnd.", el cual presenta un avance de inicio de ".$AcValue."%.", 6 , 1, $apiDataUrl, $user);                        
            $action->addResponsible($user);            
        }
        
        $action->setRef($ref);//referencia
        $action->setCreatedBy($user);
        $action->setEvolutionCause($causeResult);
        $action->setMonth($data);//Carga de Mes(var month)
        $action->setTypeObject($typeObject);//Tipo de Objeto        

        //if ($form->isSubmitted()) {                
        $em->persist($action);
        $em->flush();                
        
        try {
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
        throw $e;
        }
        //} 

        $idAction = $action->getId();               
        if ($idAction) {
            for ($i=$dStart; $i <= $dEnd; $i++) {                 
                    $action = $this->get('pequiven.repository.sig_action_indicator')->find($idAction);
                    $relactionValue = new EvolutionActionValue();
                    $relactionValue->setAdvance($AcValue);
                    $relactionValue->setObservations($AcObservation);
                    $relactionValue->setMonth($data);
                    $relactionValue->setActionValue($action);

                    $em->persist($relactionValue);
                    $em->flush();

                $count = $count + 1;
                $data = $dStart + $count;
                $AcObservation = null;                    
            }
        }
        $this->get('session')->getFlashBag()->add('success', "Plan de Acción Cargado Exitosamente");            
    
    }

    /**
     * Elimina las acciones
     * 
     * @param Request $request
     * @return type
     */
    public function deletePlanAction(Request $request)
    {           
        $idAction = $request->get('id');
        
        $em = $this->getDoctrine()->getManager();
        $results = $this->get('pequiven.repository.sig_action_indicator')->find($idAction);
        
        if($results){
            $em->remove($results);
            $em->flush();            
            $this->get('session')->getFlashBag()->add('success', $this->trans('flashes.messages.deleteAction', array(), 'PequivenSIGBundle'));       
        }  
    }

    /**
     * Añade Valores del Plan de Acción en Acciones Heredadas
     * 
     * @param Request $request
     * @return type
     */
    public function addValuesAction(Request $request)
    {    
        $idAction = $request->get('idAction'); //Recibiendo de $request el id del valor        
        
        $month = $this->getRequest()->get('month');
        
        $actionResults = $this->get('pequiven.repository.sig_action_value_indicator')->findBy(array('actionValue'=> $idAction));
        $cant = count($actionResults);
        
        //Recibiendo de formulario
        $AcValue = $request->get('actionValue')['advance'];//RecibiendoValue
        $AcObservation = $request->get('actionValue')['observations'];//RecibiendoObservations
        
        for ($i=0; $i <= $cant ; $i++) { //for segun la cantidad de acciones       
            //Consultando valores
            $actionResult = $this->get('pequiven.repository.sig_action_value_indicator')->findBy(array('actionValue'=> $idAction, 'month'=> $month));
            foreach ($actionResult as $value) {
                $dat = $value->getMonth();                
                if ($value->getMonth() == $month) {//Si la accion tiene el mes igual al pasado la actualiza                
                    $sumAdvance = $value->getAdvance() + $AcValue;                
                    
                    $value->setAdvance($sumAdvance);
                    $value->setObservations($AcObservation);
                            
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();                   

                    $month = $month + 1;//Carga de los meses tantas veces sean para la consulta
                    $AcObservation = null;//
                }
            }
        }
        $this->get('session')->getFlashBag()->add('success', "Avance Cargado Exitosamente");                        
    }    

    /**
     * Retorna el formulario de Verificación del Plan de Acción
     * 
     * @param Request $request
     * @return type
     */
    function getFormVerificationAction(Request $request)
    {
        $id = $request->get('id');        
        $typeObject = $request->get('typeObj');
        
        $month = $request->get('evolutiontrend')['month'];//Carga de Mes pasado
        
        $user = $this->getUser();        
        
        $verification = new EvolutionActionVerification();
        $form  = $this->createForm(new EvolutionActionVerificationType($id, $typeObject), $verification);        
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('form/form_action_verification.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'indicator' => $id,
                'form' => $form->createView(),
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     * Añade la Verificacion de Plan de Acción y Seguimiento
     * 
     * @param Request $request
     * @return type
     */
    public function addVerificationAction(Request $request)
    {   
        $id = $request->get('idIndicator');//Carga de indicador
        $typeObject = $request->get('typeObj');
        $month = $request->get('month');;//Mes
        $user = $this->getUser();

        $verification = new EvolutionActionVerification();
        $form  = $this->createForm(new EvolutionActionVerificationType(), $verification);
        
        if ($typeObject == 1) {            
            $repository = $this->get('pequiven.repository.sig_indicator');
            $results = $repository->find($id);            
            $verification->setIndicator($results);            
        }elseif($typeObject == 2){            
            $repository = $this->get('pequiven_seip.repository.arrangementprogram');
            $results = $repository->find($id); 
            $verification->setArrangementProgram($results);            
        }

        $action = $request->get('actionVerification')['actionPlan'];
        $idVerification = $request->get('actionVerification')['typeVerification'];
        
        //Consulta del tipo de Verificación para el plan de acción
        $ver = $this->get('pequiven.repository.managementsystem_sig_verification')->find($idVerification);
        $statusAction = $ver->getStatus();//Status 0/1 para el plan 

        //Acción
        $actionVer = $this->get('pequiven.repository.sig_action_indicator')->find($action);
        
        $verification->setCreatedBy($user);
        $verification->setActionPlan($actionVer);
        $verification->setMonth($month);
        $verification->setTypeObject($typeObject);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            
            $em = $this->getDoctrine()->getManager();
            $em->persist($verification);
            $em->flush();
        }    
        
        if($actionVer){//Si existe la acción le cambio el status segun sea el caso
            $actionVer->setStatus($statusAction);
            $em->flush();  
        }  
        $this->get('session')->getFlashBag()->add('success', "Verificación Cargada Exitosamente");                                
    }

    /**
     *  Delete verification
     *  
     * @param Request $request
     * @return type
     */
    public function deleteVerificationAction(Request $request)
    {
        $id = $request->get('id');//id Verification        
        
        $em = $this->getDoctrine()->getManager();
        $verification = $this->get('pequiven.repository.sig_action_verification')->find($id);
        
        if($verification){
            $em->remove($verification);
            $em->flush(); 
            $this->get('session')->getFlashBag()->add('success', "Verificación Eliminada Exitosamente");                        
        }

    }

    /**
     * Buscamos las acciones de las causas
     * @param Request $request
     * @return \Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCauses
     * @throws type
     */
    private function findEvolutionCause(Request $request)
    {
        $idIndicator = $request->get('id'); 
        //Mes Actual
        $monthActual = date("m");
        //Mes Consultado       
        $month = $request->get('month'); 
        //Carga de variable base
        $opc = false; $idAction = $actionResult = 0; $idCons = [0];
        //$results = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $idIndicator,'month'=> $month));
        $results = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $idIndicator));
  
        //Determinando si esta en historico de informe o periodo actual
        if($month < $monthActual){
            $statusCons = 1;
        }else{
            $statusCons = 0;
        }
        
        $cause = array();
        if($results){
            foreach ($results as $value) {                
                $idCause = $value->getId();                
                $cause[] = $idCause;
            }
            $action = $this->get('pequiven.repository.sig_action_indicator')->findBy(array('evolutionCause' => $cause));             
        }        
        
        if(!$results){
            $action = null;
        }

        //Carga de las acciones para sacar la verificaciones realizadas
        if($action){         
            foreach ($action as $value) {                
                $relation = $value->getRelactionValue();                    
                    foreach ($relation as $value) {                            
                            $monthAction = $value->getMonth();
                            $monthGet = (int)$month;
                        if ($monthAction === $monthGet) {                            
                            $idAction = $value->getActionValue()->getId();
                            $idCons[] = $idAction;
                        }            
                    }
            }
            $actionResult = $this->get('pequiven.repository.sig_action_indicator')->findBy(array('id' => $idCons));
        }  

        $actionsValues = $this->get('pequiven.repository.sig_action_value_indicator')->findBy(array('actionValue' => $idCons, 'month' => $month));    
        //Carga de Cantidiad de Acciones
        $cant = "0".''.count($actionResult);
        
        if($opc = false){
            $idAction = null;
        } 
        $verification = $this->get('pequiven.repository.sig_action_verification')->findBy(array('indicator'=>$idIndicator, 'month' => $month));                
        
        //Carga de array con la data
        $data = [
            'action'        => $actionResult, //Pasando la data de las acciones si las hay
            'verification'  => $verification, //Pasando la data de las verificaciones
            //'results'     => $results //Pasando la data de las causas si las hay
            'actionValue'   => $actionsValues,
            'cant'          => $cant
        ];

        return $data;
    } 

    /**
     * Creando la Referencia de la acción
     * @param Request $request
     * @return \Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCauses
     * @throws type
     */
    private function findEvolutionActionRef(Request $request, $monthSet, $causeAction)
    {
        $id = $request->get('idIndicator');
        $typeObject = $request->get('typeObj');
        $cont = 1; 
        $posCause = 1;        
        
        //Mes Actual
        $monthActual = date("m");
        //Mes Consultado       
        $month = $request->get('month'); 
        
        if ($typeObject == 1) {
            
            $result = $this->get('pequiven.repository.indicator')->find($id);             
            //Causa
            $causes = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $id, 'month' => $monthSet));
                
                $cause = array();
                if ($causes) {
                    foreach ($causes as $value) {
                        $idCause = $value->getId();
                        $cause[] = $idCause;
                        if ($idCause == $causeAction) {
                            $posCause = $cont;
                        }
                        $cont++; 
                    }
                    $action = $this->get('pequiven.repository.sig_action_indicator')->findBy(array('evolutionCause' => $cause));
                }
                if (!isset($action)) {
                    $this->get('session')->getFlashBag()->add('error', "Plan de Acción no Cargado! Ha seleccionado un mes que no posee Causas Cargadas"); 
                    die();                                           
                }
                $cantAction = count($action) + 1;
                $cantAction = "0".''.$cantAction.''."-";                

            foreach ($result->getObjetives() as $value) {                
                $compData = $value->getComplejo();//Consultando si tiene complejo
                $gerData = $value->getGerencia();//Si tiene gerencia
                
                if($compData){
                    $complejo = $compData->getRef().''."-";
                    $complejo = strtoupper($complejo);
                }else{
                    $complejo = "S/C-";                    
                }
                if ($gerData) {                
                    $gerencia = $gerData->getAbbreviation().''."-"; 
                    $gerencia = strtoupper($gerencia);
                }else{
                    $gerencia = "S/G-";                 
                }
            }
            
        }elseif($typeObject == 2){            
            $repository = $this->get('pequiven_seip.repository.arrangementprogram');
            $result = $repository->find($id); 
            //Causas
            $cause = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('arrangementProgram' => $id));
            $complejo = "S/C";
            $gerencia = "S/G";
            $cantAction = 0;
        }

        $monthSet = $monthSet.''."-";
        $posCause = "0".''.$posCause;
        $ref = $complejo.''.$gerencia.''.$monthSet.''.$cantAction.''.$posCause;
        //Carga de array con la data
        $data = [
            //'id'    => $id,
            'ref'   => $ref
        ];

        return $ref;
    } 

    /**
     * Obtiene los responsable que se pueden asignar a un plan
     */
    function getResponsiblesToPlanAction(\Symfony\Component\HttpFoundation\Request $request) {        
        $query = $request->get('query');
        $results = array();        
        
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
        $repository = $this->get('pequiven_seip.repository.user');        
        $users = $repository->findAll();        
        if (!$users) {
            throw $this->createNotFoundException();
        }
        
        $criteria = array(
            'username' => $query,
            'firstname' => $query,
            'lastname' => $query,
            'numPersonal' => $query,                
        );
            
        $results = $this->get('pequiven_seip.repository.user')->findToAssingTacticArrangementProgramGoal($users, $criteria);        

        $view = $this->view();
        $view->setData($results);
        $view->getSerializationContext()->setGroups(array('id', 'api_list', 'sonata_api_read'));
        return $this->handleView($view);
    }

    /**
     * Busca el indicador o retorna un 404
     * @param Request $request
     * @return \Pequiven\IndicatorBundle\Entity\Indicator
     * @throws type
     */
    private function findIndicatorOr404(Request $request)
    {
        $id = $request->get('idIndicator');
        
        $indicator = $this->get('pequiven.repository.indicator')->find($id);
        if(!$indicator){
            throw $this->createNotFoundException();
        }
        return $indicator;
    }
    
    /**
     * 
     * @return \Pequiven\SEIPBundle\Service\SecurityService
     */
    protected function getSecurityService() {

        return $this->container->get('seip.service.security');
    }  

    /**
     * @return \Pequiven\SEIPBundle\Service\FusionChartExportService
     */
    private function getFusionChartExportService()
    {
        return $this->container->get('pequiven_seip.service.fusion_chart');
    } 

    /**
     * 
     * @return \Pequiven\IndicatorBundle\Service\IndicatorService
     */
    protected function getIndicatorService() {
        return $this->container->get('pequiven_indicator.service.inidicator');
    } 

    /**
     *  Notification
     *
     */
    protected function getNotificationService() {        
        return $this->container->get('seip.service.notification');        
    }
   
}
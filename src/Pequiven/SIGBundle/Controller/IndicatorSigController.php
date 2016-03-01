<?php

namespace Pequiven\SIGBundle\Controller;

use Pequiven\IndicatorBundle\Entity\Indicator;
use Pequiven\SIGBundle\Entity\ManagementSystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tecnocreaciones\Bundle\ResourceBundle\Controller\ResourceController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Pequiven\IndicatorBundle\Entity\IndicatorLevel;

use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCause;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionCauseType;
use Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionTrend;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionTrendType;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\IndicatorLastPeriodType;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\IndicatorConfigSigType;
use Pequiven\IndicatorBundle\Form\EvolutionIndicator\EvolutionIndicatorCloningType;

/**
 * Controlador Informe de Evolución del Indicador
 *
 * @author Maximo Sojo <maxsojo13@gmail.com>
 */
class IndicatorSigController extends ResourceController {

    /**
     * Lista de Indicadores por nivel(Estratégico, Táctico u Operativo)
     * Filtrados por managementSystems
     * @param Request $request
     * @return type
     */
    function listAction(Request $request) {

        $level = $request->get('level');

        $rol = null;
        $roleByLevel = array(
            IndicatorLevel::LEVEL_ESTRATEGICO => array('ROLE_SEIP_SIG_INDICATOR_LIST'),
            IndicatorLevel::LEVEL_TACTICO => array('ROLE_SEIP_SIG_INDICATOR_LIST'),
            IndicatorLevel::LEVEL_OPERATIVO => array('ROLE_SEIP_SIG_INDICATOR_LIST')
        );
        if (isset($roleByLevel[$level])) {
            $rol = $roleByLevel[$level];
        }

        $this->getSecurityService()->checkSecurity($rol);

        $criteria = $request->get('filter', $this->config->getCriteria());
        $sorting = $request->get('sorting', $this->config->getSorting());
        $repository = $this->container->get('pequiven.repository.sig_indicator');
        
        $criteria['indicatorLevel'] = $level;
        $criteria['applyPeriodCriteria'] = true;

        if ($this->config->isPaginated()) {
            $resources = $this->resourceResolver->getResource(
                    $repository, 'createPaginatorByLevelSIG', array($criteria, $sorting)
            );


            $maxPerPage = $this->config->getPaginationMaxPerPage();
            if (($limit = $request->query->get('limit')) && $limit > 0) {
                if ($limit > 100) {
                    $limit = 100;
                }
                $maxPerPage = $limit;
            }
            $resources->setCurrentPage($request->get('page', 1), true, true);
            $resources->setMaxPerPage($maxPerPage);
        } else {
            $resources = $this->resourceResolver->getResource(
                    $repository, 'findBy', array($criteria, $sorting, $this->config->getLimit())
            );
        }
        $routeParameters = array(
            '_format' => 'json',
            'level' => $level,
        );
        $apiDataUrl = $this->generateUrl('pequiven_indicatorsig_list', $routeParameters);
        $view = $this
                ->view()
                ->setTemplate($this->config->getTemplate('list.html'))
                ->setTemplateVar($this->config->getPluralResourceName())
        ;

        $view->getSerializationContext()->setGroups(array('id', 'api_list', 'valuesIndicator', 'managementSystems', 'api_details', 'sonata_api_read', 'formula'));
        if ($request->get('_format') == 'html') {
            $labelsSummary = array();
            foreach (Indicator::getLabelsSummary() as $key => $value) {
                $labelsSummary[] = array(
                    'id' => $key,
                    'description' => $this->trans($value, array(), 'PequivenSIGBundle'),
                );
            }

            $data = array(
                'apiDataUrl' => $apiDataUrl,
                $this->config->getPluralResourceName() => $resources,
                'level' => $level,
                'labelsSummary' => $labelsSummary
            );
            $view->setData($data);
        } else {
            $formatData = $request->get('_formatData', 'default');

            $view->setData($resources->toArray('', array(), $formatData));
        }
        return $this->handleView($view);
    }

    /**
     * Vista indice de evolucion
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function evolutionAction(Request $request) {
        
        $resource = $this->findOr404($request);
        $form = $this->getForm($resource);

        $sumCause = 0; $typeObject = 1;

        $idIndicator = $request->get('id');

        $indicator = $this->get('pequiven.repository.indicator')->find($idIndicator); //Obtenemos el indicador        
        
        //Validación para Visualizar informe de Evolución
        if (!$indicator->getShowEvolutionView()) {
            $this->get('session')->getFlashBag()->add('success', 'Indicador no Habilitado para Visualizar Informe de Evolución');
            return $this->redirect($this->generateUrl("pequiven_indicator_show", array("id" => $indicator->getId())));         
        }

        $indicatorBase = $indicator;
        //Seteo de Indicador a clonar
        if ($indicator->getParentCloning()) {
            $indicator = $indicator->getParentCloning();    
            $idIndicator = $indicator->getId();                
                if ($indicator->getParentCloning()) {                    
                    $indicator = $indicator->getParentCloning();
                    $idIndicator = $indicator->getId();                
                }            
        }
        

        $month = $request->get('month'); //El mes pasado por parametro
        $data = $this->findEvolutionCause($indicator, $request); //Carga la data de las causas y sus acciones relacionadas
       
        //Validación de que el mes pasado este entre los validos
        if ($month > 12) {
            $this->get('session')->getFlashBag()->add('error', "El mes consultado no es un mes valido!");
            $month = 12;
        } elseif ($month < 1) {
            $this->get('session')->getFlashBag()->add('error', "El mes consultado no es un mes valido!");
            $month = 01;
        }

        //Cargando el Archivo
        $uploadFile = $request->get("uploadFile"); //Recibiendo archivo
        //SI SE SUBIO EL ARCHIVO SE PROCEDE A GUARDARLO
        if ($uploadFile != null) {
            $band = false;
            //VALIDACION QUE SEA UN ARCHIVO PERMITIDO
            foreach ($request->files as $file) {
                if (in_array($file->guessExtension(), \Pequiven\IndicatorBundle\Model\Indicator\ValueIndicatorFile::getTypesFile())) {
                    $band = true;
                }
            }
            if ($band) {
                $this->createValueCauseFile($resource, $request);
            } else {
                $this->get('session')->getFlashBag()->add('error', $this->trans('action.messages.InvalidFile', array(), 'PequivenIndicatorBundle'));
                $this->redirect($this->generateUrl("pequiven_indicator_evolution", array("id" => $request->get("id"), "month" => $month)));
            }
        }

        //Url export
        $urlExportFromChart = $this->generateUrl('pequiven_indicator_evolution_export_chart', array('id' => $request->get("id"), 'month' => $month, 'typeObj' => 1));
        
        //Carga de data de Indicador para armar grafica
        $response = new JsonResponse();

        $indicatorService = $this->getIndicatorService(); //Obtenemos el servicio del indicador
        
        $dataChart = $indicatorService->getDataChartOfIndicatorEvolution($indicatorBase,$urlExportFromChart,$month); //Obtenemos la data del gráfico de acuerdo al indicador
        
        //Carga de los datos de la grafica de las Causas de Desviación
        $dataCause = $indicatorService->getDataChartOfCausesIndicatorEvolution($indicator, $month, $urlExportFromChart); //Obtenemos la data del grafico de las causas de desviación
        
        $results = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $idIndicator, 'month' => $month));
        foreach ($results as $value) {
            $dataCa = $value->getValueOfCauses();
            $sumCause = $sumCause + $dataCa;
        }

        //Carga el analisis de la tendencia
        $trend = $this->get('pequiven.repository.sig_trend_report_evolution')->findBy(array('indicator' => $indicator, 'month' => $month, 'typeObject' => 1));
        //Carga del analisis de las causas
        $causeAnalysis = $this->get('pequiven.repository.sig_causes_analysis')->findBy(array('indicator' => $indicator, 'month' => $month));
        //Carga de la señalización de la tendencia de la grafica        
        $tendency = $indicator->getTendency()->getId();
        $font = array();
        switch ($tendency) {
            case 0:
                $font = [
                    'icon' => '',
                    'text' => ''
                ];
                break;
            case 1:
                $font = [
                    'icon' => 'long-arrow-up',
                    'text' => 'Mejor hacia...'
                ];
                break;
            case 2:
                $font = [
                    'icon' => 'long-arrow-down',
                    'text' => 'Mejor hacia...'
                ];
                break;
            case 3:
                $font = [
                    'icon' => 'long-arrow-right',
                    'text' => 'Estable...'
                ];
                break;
        }

        $dataAction = [
            'action' => $data["action"],
            'values' => $data["actionValue"]
        ];

        $view = $this
                ->view()
                ->setTemplate($this->config->getTemplate('evolution.html'))
                ->setData(array(
            'data' => $dataChart,
            'verification' => $data["verification"],
            'dataCause'  => $dataCause,
            'sumCause'   => $sumCause,
            'cause'      => $results,
            'month'      => $month,
            'dataAction' => $dataAction,
            'analysis'   => $causeAnalysis,
            'trend'      => $trend,
            'font'       => $font,
            'typeObject' => $typeObject,
            'id'         => $idIndicator,
            'route'      => "pequiven_indicator_evolution", //Ruta para carga de Archivo
            'urlExportFromChart' => $urlExportFromChart,
            $this->config->getResourceName() => $resource,
            'form' => $form->createView()
        ));

        return $this->handleView($view);
    }

    public function createValueCauseFile(Indicator $indicator, Request $request) {

        $EvolutionCauseFile = new Indicator\EvolutionIndicator\EvolutionCauseFile();

        $fileUploaded = false;

        $month = date("m"); //Carga del mes de Creación de la causa "Automatico"  

        $causeAnalysis = $request->get('cause');

        $causeData = $this->get('pequiven.repository.sig_causes_analysis')->find($causeAnalysis);

        if ($causeData->getId() == $causeAnalysis) {

            $EvolutionCauseFile->setValueCause($causeData);
            foreach ($request->files as $file) {
                //VALIDA QUE EL ARCHIVO SEA UN PDF
                //SE GUARDAN LOS CAMPOS EN BD
                $EvolutionCauseFile->setCreatedBy($this->getUser());
                $EvolutionCauseFile->setNameFile($file->getClientOriginalName());
                $EvolutionCauseFile->setPath(Indicator\EvolutionIndicator\EvolutionCauseFile::getUploadDir());
                $EvolutionCauseFile->setExtensionFile($file->guessExtension());

                //SE MUEVE EL ARCHIVO AL SERVIDOR
                $file->move($this->container->getParameter("kernel.root_dir") . '/../web/' . Indicator\EvolutionIndicator\EvolutionCauseFile::getUploadDir(), Indicator\ValueIndicator\ValueIndicatorFile::NAME_FILE . $causeData->getId());
                $fileUploaded = $file->isValid();
            }
        }

        if (!$fileUploaded) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($EvolutionCauseFile);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', $this->trans('action.messages.saveFileSuccess', array(), 'PequivenIndicatorBundle'));
            $request->request->set("uploadFile", "");
            $this->redirect($this->generateUrl("pequiven_indicator_evolution", array("id" => $request->get("id"), "month" => $month)));
        } else {
            $this->get('session')->getFlashBag()->add('error', $this->trans('action.messages.errorFileUpload', array(), 'PequivenIndicatorBundle'));
            $request->request->set("uploadFile", "");
            $this->redirect($this->generateUrl("pequiven_indicator_evolution", array("id" => $request->get("id"), "month" => $month)));
        }
    }

    /**
     *
     * Generate URL files
     * 
     */
    public function generateUrlFile(Request $request) {

        $response = new JsonResponse();
        $data = array();
        $data["url"] = $this->generateUrl("pequiven_indicator_vizualice_file", array("id" => $request->get("id")));
        $response->setData($data);
        return $response;
    }

    /**
     * Retorna el formulario de la relacion del indicador con periodo 2014
     * 
     * @param Request $request
     * @return type
     */
    function getFormAction(Request $request) {
        $indicator = $this->findIndicatorOr404($request);

        $indicatorRel = new Indicator();
        $form = $this->createForm(new IndicatorLastPeriodType(), $indicatorRel);

        $view = $this
                ->view()
                ->setTemplate($this->config->getTemplate('form/form.html'))
                ->setTemplateVar($this->config->getPluralResourceName())
                ->setData(array(
            'indicator' => $indicator,
            'form' => $form->createView(),
                ))
        ;
        $view->getSerializationContext()->setGroups(array('id', 'api_list'));
        return $view;
    }

    /**
     * Añade la relacion con el indicador 2014
     * 
     * @param Request $request
     * @return type
     */
    public function addLastPeriodAction(Request $request) {
        $idIndicator = $request->get('idIndicator');

        $lastPeriod = $request->get('lastPeriod')['indicatorlastPeriod'];
        
        $em = $this->getDoctrine()->getManager();
        $indicatorRel = $this->get('pequiven.repository.sig_indicator')->find($idIndicator);

        if ($indicatorRel) {
            $dataLast = $this->get('pequiven.repository.sig_indicator')->find($lastPeriod);
        }

        $indicatorRel->setIndicatorLastPeriod($dataLast);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Relación Cargada Correctamente");
    }

    /**
     * Elimina la relación con el Indicador 2014
     * 
     * @param Request $request
     * @return type
     */
    public function deleteLastPeriodAction(Request $request) {
        $idIndicator = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $indicatorRel = $this->get('pequiven.repository.sig_indicator')->find($idIndicator);

        if ($indicatorRel) {
            $dataLast = NULL;
        }

        $indicatorRel->setIndicatorLastPeriod($dataLast);

        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Relación Eliminada Correctamente");
    }

    /**
     * Retorna el formulario de configuracion de la grafica del infome de evolución
     * 
     * @param Request $request
     * @return type
     */
    function getFormConfigAction(Request $request)
    {
        $id = $request->get('idIndicator');        
        //$typeObject = $request->get('typeObj');
        //$month = $request->get('evolutiontrend')['month'];//Carga de Mes pasado        
        
        $indicator = new indicator();
        $form  = $this->createForm(new IndicatorConfigSigType(), $indicator);
        
        if ($request->get('configSig')) {
            $em = $this->getDoctrine()->getManager();
            $indicatorRel = $this->get('pequiven.repository.sig_indicator')->find($id);
            
            $medition = $request->get('configSig')['indicatorSigMedition'];

            $indicatorRel->setIndicatorSigMedition($medition);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Configuración de Medición Cargada Correctamente");
            die();
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('form/form_config_chart.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(                
                'form' => $form->createView(),
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     * Retorna el formulario de Carga de Indicador al que se clonara el informe de evolución
     * 
     * @param Request $request
     * @return type
     */
    function getFormCloningAction(Request $request)
    {
        $id = $request->get('id');        
        $indicatorData = $this->get('pequiven.repository.sig_indicator')->find($id);                    
        $period = $indicatorData->getPeriod()->getId();

        $indicator = new indicator();
        $form  = $this->createForm(new EvolutionIndicatorCloningType($period), $indicator);
        
        if ($request->get('indicatoCloning')['parentCloning']) {
            $em = $this->getDoctrine()->getManager();            
            $indicatorCloning = $this->get('pequiven.repository.sig_indicator')->find($request->get('indicatoCloning')['parentCloning']);                                

            $indicatorData->setParentCloning($indicatorCloning);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Datos Cargados Correctamente");
            die();
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('form/form_cloning.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(                
                'form' => $form->createView(),
            ))
        ;
        $view->getSerializationContext()->setGroups(array('id','api_list'));
        return $view;
    }

    /**
     * Buscamos las acciones de las causas
     * @param Request $request
     * @return \Pequiven\IndicatorBundle\Entity\Indicator\EvolutionIndicator\EvolutionCauses
     * @throws type
     */
    private function findEvolutionCause($indicator, $request) {
        $id = $indicator->getId();
        $typeObject = $request->get('typeObj');

        if ($typeObject === NULL) {
            $typeObject = 1;
        }

        //Mes Actual
        $monthActual = date("m");
        //Mes Consultado       
        $month = $request->get('month');
        //Carga de variable base
        $opc = false;
        $idAction = $actionResult = 0;
        $idCons = [0];

        //$results = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $idIndicator,'month'=> $month));
        if ($typeObject == 1) {
            $results = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $id));
        } elseif ($typeObject == 2) {
            $results = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('arrangementProgram' => $id));
        }

        //Determinando si esta en historico de informe o periodo actual
        if ($month < $monthActual) {
            $statusCons = 1;
        } else {
            $statusCons = 0;
        }

        $cause = array();
        if ($results) {
            foreach ($results as $value) {
                $idCause = $value->getId();
                $cause[] = $idCause;
            }
            $action = $this->get('pequiven.repository.sig_action_indicator')->findBy(array('evolutionCause' => $cause));
        }

        if (!$results) {
            $action = null;
        }

        //Carga de las acciones para sacar la verificaciones realizadas
        if ($action) {
            foreach ($action as $value) {
                $relation = $value->getRelactionValue();
                foreach ($relation as $value) {
                    $monthAction = $value->getMonth();
                    $monthGet = (int) $month;
                    if ($monthAction === $monthGet) {

                        $idAction = $value->getActionValue()->getId();
                        $idCons[] = $idAction;
                    }
                }
            }
            $actionResult = $this->get('pequiven.repository.sig_action_indicator')->findBy(array('id' => $idCons));
        }
//        $actionsValues = EvolutionActionValue::getActionValues($idCons, $month);          
        $actionsValues = $this->get('pequiven.repository.sig_action_value_indicator')->findBy(array('actionValue' => $idCons, 'month' => $month));
        $cant = count($actionResult);

        if ($opc = false) {
            $idAction = null;
        }
        if ($typeObject == 1) {
            $verification = $this->get('pequiven.repository.sig_action_verification')->findBy(array('indicator' => $id, 'month' => $month));
        } elseif ($typeObject == 2) {
            $verification = $this->get('pequiven.repository.sig_action_verification')->findBy(array('arrangementProgram' => $id, 'month' => $month));
        }

        //Carga de array con la data
        $data = [
            'action' => $actionResult, //Pasando la data de las acciones si las hay
            'verification' => $verification, //Pasando la data de las verificaciones            
            'actionValue' => $actionsValues,
            'cant' => $cant
        ];

        return $data;
    }    

    /**
     *
     *
     *
     */
    public function exportChrat(Request $request)
    {          
        if($request->isMethod('POST')){
          $exportRequestStream = $request->request->all();          
          $request->request->remove('charttype');
          $request->request->remove('stream');
          $request->request->remove('stream_type');
          $request->request->remove('meta_bgColor');
          $request->request->remove('meta_bgAlpha');
          $request->request->remove('meta_DOMId');
          $request->request->remove('meta_width');
          $request->request->remove('meta_height');
          $request->request->remove('parameters');
          $fusionchartService = $this->getFusionChartExportService();          
          $fileSVG = $fusionchartService->exportFusionChart($exportRequestStream);           
        }        

        //return $fileSVG;
        $this->exportAction($request);            

    }

    /**
     *
     * Exportar informe de Evolución
     *
     */
    public function exportAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $routing = $this->container->getParameter('kernel.root_dir')."/../web/php-export-handler/temp/*.png";
        
        //$fileSVG = $this->exportChrat($request);        
        sleep(1);
        //Buscando los Archivos por Codigo
        $nameSVG = glob("$routing");
        $user = $this->getUser()->getId();//Id Usuario    
        $user = str_pad($user, 6,"0", STR_PAD_LEFT);
        
        $cont = 0;
        $contImg = 1;
        foreach ($nameSVG as $value) {            
            $pos = strpos($nameSVG[$cont], $user);            
            if ($pos !== false) {                                 
                if (strpos($nameSVG[$cont], "mscolumnline3d")) {                    
                    $chartEvolution = $nameSVG[$cont];                    
                    $contImg ++;
                    //var_dump("1");
                }
                if (strpos($nameSVG[$cont], "stackedbar3d")) {
                    $chartCause = $nameSVG[$cont];                                        
                    //var_dump("2");
                }
            }
            $cont ++;
        }        
        
        $id = $request->get('id'); //id         
        $indicator = $this->get('pequiven.repository.indicator')->find($id); //Obtenemos el indicador
        $indicatorBase = $indicator;

        //si el indicador es clonado
        if ($indicator->getParentCloning()) {
            $id = $indicator->getParentCloning()->getId();            
            $indicator = $indicator->getParentCloning();
            //Si el indicador es clonado y viene del estratetico
            if ($indicator->getParentCloning()) {
                $id = $indicator->getParentCloning()->getId();
                $indicator = $indicator->getParentCloning();
            }
        }

        $dataAction = $this->findEvolutionCause($indicator, $request); //Carga la data de las causas y sus acciones relacionadas
        $month = $request->get('month'); //El mes pasado por parametro
        $typeObject = $request->get('typeObj'); //Tipo de objeto (1 = Indicador/2 = Programa G.) 
        
        $font = "";
        if ($typeObject == 1) {            
            $name = $indicatorBase->getRef() . ' ' . $indicatorBase->getDescription(); //Nombre del Indicador
            $type = "indicator";
            //Relación - Objetivo
            foreach ($indicator->getObjetives() as $value) {
                $objRel = $value->getDescription();
            }
            $routingTendency = "/../web/bundles/pequivensig/images/";//Ruta de la Tendencia
            $tendency = $indicator->getTendency()->getId();
            switch ($tendency) {
                case 0:
                    $font = "";
                    break;
                case 1:
                    $font = "1.png";
                    break;
                case 2:
                    $font = "2.png";
                    break;
                case 3:
                    $font = "3.png";
                    break;
            }
            $font = $routing.$routingTendency.$font;            

        } elseif ($typeObject == 2) {
            $ArrangementProgram = $em->getRepository('PequivenArrangementProgramBundle:ArrangementProgram')->findWithData($id);
            $type = "arrangementProgram";
            $name = $ArrangementProgram->getRef() . '' . $ArrangementProgram->getDescription();
            //Relacion
            $objRel = $ArrangementProgram->getTacticalObjective()->getDescription();
        }
        
        $formula = $indicator->getFormula();
        
        //Carga el analisis de la tendencia
        $trend = $this->get('pequiven.repository.sig_trend_report_evolution')->findBy(array($type => $id, 'month' => $month));
        if ($trend) {
            foreach ($trend as $value) {
                $trendDescription = $value->getDescription(); //Tendencia
            }
        } else {
            $trendDescription = "No se ha Cargando el Analisis de Tendencia";
        }

        //Carga del analisis de las causas
        $causeAnalysis = $this->get('pequiven.repository.sig_causes_analysis')->findBy(array($type => $id, 'month' => $month));
        if ($causeAnalysis) {
            foreach ($causeAnalysis as $value) {
                $causeA = $value->getDescription();
            }
        } else {
            $causeA = "No se ha Cargando el Analisis de Causas";
        }

        //Verificación
        $verification = $this->get('pequiven.repository.sig_action_verification')->findBy(array($type => $id, 'month' => $month));

        //Periodo
        $period = $this->getPeriodService()->getPeriodActive();
        
            $data = array(
                'formula'       => $formula,            
                'nameSVG'       => $chartEvolution,
                'chartCause'    => $chartCause,
                'month'         => $month,
                'name'          => $name,
                'trend'         => $trendDescription,
                'causeAnalysis' => $causeA,
                'dataAction'    => $dataAction["actionValue"],
                'obj'           => $objRel,
                'verification'  => $verification,
                'period'        => $period,
                'font'          => $font
            );

        //Solo si existen las dos graficas
        //if (isset($chartEvolution) AND isset($chartCause)) {            
            $this->generatePdf($data);            
        //}
    }

    public function generatePdf($data) {
        $pdf = new \Pequiven\SIGBundle\Model\PDF\SIGPdf('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintLineFooter(false);
        $pdf->setContainer($this->container);
        //$pdf->setPeriod($this->getPeriodService()->getPeriodActive());
        //$pdf->setFooterText($this->trans('pequiven_seip.message_footer', array(), 'PequivenSEIPBundle'));
    // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SIG');
        $pdf->setTitle('INFORME DE EVOLUCIÓN');
        $pdf->SetSubject('Resultados SIG');
        $pdf->SetKeywords('PDF, SIG, Resultados');

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set font
    //  $pdf->SetFont('times', 'BI', 12);
    // add a page        
        $pdf->AddPage('L');

    // set some text to print 
        $html = $this->renderView('PequivenSIGBundle:Indicator:viewPdf.html.twig', $data);

    // print a block of text using Write()
        $pdf->writeHTML($html, true, false, true, false, '');

    //            $pdf->Output('Reporte del dia'.'.pdf', 'I');
        $pdf->Output('Informe de evolucion' . '.pdf', 'D');

        $this->rmTempFile($data);
    }
    /**
     *
     *  Eliminación de Archivos temporales
     *
     */
    public function rmTempFile($data)
    {   
        $imgChart = $data['nameSVG'];//Ruta
        $imgCause = $data['chartCause'];//Ruta
        
        shell_exec("rm $imgChart");//Eliminamos
        shell_exec("rm $imgCause");//Eliminamos

    }

    /**
     * Busca el indicador o retorna un 404
     * @param Request $request
     * @return \Pequiven\IndicatorBundle\Entity\Indicator
     * @throws type
     */
    private function findIndicatorOr404(Request $request) {
        $id = $request->get('idIndicator');

        $indicator = $this->get('pequiven.repository.indicator')->find($id);
        if (!$indicator) {
            throw $this->createNotFoundException();
        }
        return $indicator;
    }

    public function loadAction(Request $request){

        $levels = [
            1 => "Indicadores Estratégicos",
            2 => "Indicadores Tácticos",
            3 => "Indicadores Operativos",            
        ];
        
        $cont = $trendData = $causeData = $cause = $actionData = $level = 0;
        $dataStrategic = $dataTactic = $dataOperative = $totalStrategic = $totalTactic = $totalOperative = 0;
        $dataStrategicIn = $dataTacticIn = $dataOperativeIn = [];
        $indicatorsStrategic = $indicatorsTactic = $indicatorsOperatives = [];
        
        if ($request->get('m')) {            
            for ($i=1; $i <= count($levels); $i++) {             
                $indicators = $this->get('pequiven.repository.indicator')->findQueryIndicatorValid($this->getPeriodService()->getPeriodActive(), $i);                                    
                foreach ($indicators as $valueIndicator) {
                    $trendAnalysis = $this->get('pequiven.repository.sig_trend_report_evolution')->findBy(array('indicator' => $valueIndicator->getId(), 'month' => $request->get('m'), 'typeObject' => 1));        
                    if ($trendAnalysis) {
                        $trendData = $trendData + count($trendAnalysis);
                    }
                    if ($i == 1) {
                        $totalStrategic++;
                        if ($trendAnalysis or $valueIndicator->getParentCloning()) {
                            $dataStrategic = $dataStrategic + 1;                                    
                            $indicatorsStrategic[] = $valueIndicator;                    
                        }else{
                            $dataStrategicIn[] = $valueIndicator;
                        }
                    }elseif ($i == 2) {
                        $totalTactic++;
                        if ($trendAnalysis or $valueIndicator->getParentCloning()) {
                            $dataTactic = $dataTactic + 1;                                    
                            $indicatorsTactic[] = $valueIndicator;                                            
                        }else{
                            $dataTacticIn[] = $valueIndicator;
                        }
                    }elseif ($i == 3) {
                        $totalOperative++;
                        if ($trendAnalysis or $valueIndicator->getParentCloning()) {
                            $dataOperative = $dataOperative + 1;                        
                            $indicatorsOperatives[] = $valueIndicator;                                            
                        }else{
                            $dataOperativeIn[] = $valueIndicator;
                        }
                    }
                    $causeAnalysis = $this->get('pequiven.repository.sig_causes_analysis')->findBy(array('indicator' => $valueIndicator->getId(), 'month' => $request->get('m')));
                    if ($causeAnalysis) {
                        $causeData = $causeData + count($causeAnalysis);                
                        //$causeData = $causeData + count($causeAnalysis);
                    }

                    $causes = $this->get('pequiven.repository.sig_causes_report_evolution')->findBy(array('indicator' => $valueIndicator->getId(), 'month' => $request->get('m')));                                    
                    if ($causes) {
                        $cause = $cause + count($causes);
                        //$cause = $cause + count($causes);
                    }
                    foreach ($causes as $key => $valueCause) {
                        $action = $this->get('pequiven.repository.sig_action_indicator')->findBy(array('evolutionCause' => $valueCause->getId()));
                        if ($action) {
                            $actionData = $actionData + count($action);
                            //$actionData = $actionData + count($action);
                        }
                    }
                    $cont++;                                           
                }
            }
            
            $dataIndicators = [
                1 => $dataStrategic,
                2 => $dataTactic,
                3 => $dataOperative
            ];

            $dataIndicatorsInLoad = [
                1 => $dataStrategicIn,
                2 => $dataTacticIn,
                3 => $dataOperativeIn
            ];

            $dataTotal = [
                1 => $totalStrategic, 
                2 => $totalTactic,
                3 => $totalOperative
            ];

            $indicators = [
                1 => $indicatorsStrategic,
                2 => $indicatorsTactic,
                3 => $indicatorsOperatives
            ];

            $dataGeneral = [
                1 => "Analisis de Tendencias Cargados: " . $trendData,
                2 => "Analisis de Causas Cargados: " . $causeData,
                3 => "Causas Cargadas: " . $cause,
                4 => "Planes de Acción Cargados: " . $actionData
            ];
            $data = [                                    
                'dataIndicators'       => $dataIndicators,
                'dataTotal'            => $dataTotal,
                'indicators'           => $indicators,
                'dataGeneral'          => $dataGeneral,
                'dataIndicatorsInLoad' => $dataIndicatorsInLoad,
                'value'                => 1,
                'month'                => $request->get('m'),
                'host'  => $_SERVER["HTTP_HOST"]
            ];
        }else{
            $data = [
                'value' => 0,
                'host'  => $_SERVER["HTTP_HOST"]
            ];            
        }        
        
        return $this->render('PequivenSIGBundle:Indicator:load.html.twig', array('data' => $data, 'levels' => $levels));
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
    private function getFusionChartExportService() {
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
     *  Period
     *
     */
    protected function getPeriodService() {
        return $this->container->get('pequiven_seip.service.period');
    }

}

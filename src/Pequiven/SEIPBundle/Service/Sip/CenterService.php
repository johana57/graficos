<?php

namespace Pequiven\SEIPBundle\Service\Sip;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ErrorException;
use Exception;
use LogicException;
use Pequiven\SEIPBundle\Model\Common\CommonObject;

class CenterService implements ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     *
     *
     *
     */
    public function AsignedIdEdo($estado){
        //ASIGNO ID A ESTADOS PARA MEJOR VISTA DE RUTA
        if ($estado == "EDO. CARABOBO") {
            $estado = 7;
        }elseif ($estado == "EDO. ZULIA") {
            $estado = 21;
        }elseif ($estado == "EDO. ANZOATEGUI") {
            $estado = 2;
        }elseif($estado == "OTROS"){
            $estado = 30;
        }

        return $estado;
    }

    /**
     *
     *
     *
     */
    public function AsignedDescriptionEdo($estado){
        //ASIGNO ID A ESTADOS PARA MEJOR VISTA DE RUTA
        if ($estado == 7) {
            $estado = "EDO. CARABOBO";
        }elseif ($estado == 21) {
            $estado = "EDO. ZULIA";
        }elseif ($estado == 2) {
            $estado = "EDO. ANZOATEGUI";
        }elseif($estado == 30){
            $estado = "OTROS";
        }

        return $estado;
    }

    /**
     *
     *	Grafica General
     *
     */
    public function getDataChartOfVotoGeneral($type) {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();
        
        $chart["captionFontColor"] = "#e20000";
        $chart["sYAxisName"] = "";
        $chart["sNumberSuffix"] = "";
        $chart["sYAxisMaxValue"] = "100";
        $chart["paletteColors"] = "#e20000,#0075c2,#1aaf5d,#e20000,#f2c500,#f45b00,#8e0000";
        $chart["bgColor"] = "#ffffff";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["bgAlpha"] = "0,0";//Fondo 
        $chart["legendShadow"] = "0";
        $chart["showHoverEffect"] = "0";
        $chart["valueFontColor"] = "#ffffff";
        $chart["valuePosition"] = "ABOVE";
        $chart["rotateValues"] = "1";
        $chart["placeValuesInside"] = "0";
        $chart["divlineColor"] = "#999999";
        $chart["divLineDashed"] = "1";
        $chart["divLineDashLen"] = "1";
        $chart["divLineGapLen"] = "1";
        $chart["canvasBgColor"] = "#ffffff";
        $chart["captionFontSize"] = "20";
        $chart["subcaptionFontSize"] = "14";
        $chart["subcaptionFontBold"] = "0";
        $chart["decimalSeparator"] = ",";
        $chart["thousandSeparator"] = ".";
        $chart["inDecimalSeparator"] = ",";
        $chart["inThousandSeparator"] = ".";
        $chart["decimals"] = "2";
        $chart["formatNumberScale"] = "0";
        $chart["toolTipColor"] = "#ffffff";
        $chart["toolTipBgColor"] = "#000000";
        $chart["toolTipBgAlpha"] = "0";
        $chart["toolTipBorderRadius"] = "2";
        $chart["toolTipPadding"] = "5";
        $chart["showLegend"] = "1";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";

        $em = $this->getDoctrine()->getManager();

        $label = $dataLocalidad = array();

        //Carga de Nombres de Labels
        $dataSetLocal["seriesname"] = "Voto Pequiven";
        
        $votoNo = $votoSi = 0;

        $resultVal = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstado();
        $votoNo = $resultVal[0]["SUM(votoNO)"];
        $votoSi = $resultVal[0]["SUM(votoSI)"];        

        if ($type == 1) {
            $caption = "Voto General";
            $votosOneMembers = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstadoMembers();
            $votoNo = $votoNo + $votosOneMembers[0]["SUM(votoNO)"];
            $votoSi = $votoSi + $votosOneMembers[0]["SUM(votoSI)"];            
        }elseif($type == 2){
            $caption = "Voto General PQV";
        }

        $chart["caption"] = $caption;//Nombre Grafica

            $label = "SI";
            $dataLocal["label"] = $label; //Carga de valores                
            $dataLocal["value"] = $votoSi; //Carga de valores
            $dataSetLocal["data"][] = $dataLocal; //data 

            $label = "NO";
            $dataLocal["label"] = $label; //Carga de valores                
            $dataLocal["value"] = $votoNo; //Carga de valores
            $dataSetLocal["data"][] = $dataLocal; //data 

        $data['dataSource']['chart'] = $chart;                
        $data['dataSource']['dataset'][] = $dataSetLocal;

        return json_encode($data);        
    }
    
    /**
     *
     *  Grafica de Votos por Hora
     *
     */
    public function getDataChartOfVotoGeneralLine() {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        $chart["caption"] = "Votos por Hora";
        $chart["captionFontColor"] = "#e20000";
        $chart["captionFontSize"] = "20";                
        $chart["palette"]        = "1";
        $chart["showvalues"]     = "0";
        $chart["paletteColors"]  = "#0075c2,#c90606,#f2c500,#12a830,#1aaf5d";
        $chart["showBorder"] = "0";
        $chart["yaxisvaluespadding"] = "10";
        $chart["valueFontColor"] = "#ffffff";
        $chart["rotateValues"]   = "0";
        $chart["bgAlpha"] = "0,0";//Fondo 
        $chart["theme"]          = "fint";
        $chart["showborder"]     = "0";
        $chart["decimals"]       = "0";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";
        $chart["outCnvBaseFontColor"] = "#000000";
        $chart["visible"] = "1";

        $em = $this->getDoctrine()->getManager();

        $label = $dataLinea = $dataMeta = array();

        //Carga de Nombres de Labels
        $dataSetLinea["seriesname"] = "Horas";        

        $horas = 13;
        $cont = 0;
        $horaIni = 7;

        for ($i=0; $i <= $horas; $i++) {             
            
            if ($horaIni == 13) {
                $horaIni = $horaIni - 12;
            }            

            $linea = $horaIni.":00";                      
            $label["label"] = $linea;     
            $category[] = $label;
            $horaIni++;
            $cont++;
        
        }

        $votosPrueba = 150;
        for ($i=0; $i <= $horas; $i++) {             
          
            $dataLinea["value"] = $votosPrueba; //Carga de valores
            $dataSetLinea["data"][] = $dataLinea; //data linea
            $votosPrueba = $votosPrueba * 1.5;
        }
            $dataSetValues['votos'] = array('seriesname' => 'Votos * Horas', 'parentyaxis' => 'S', 'renderas' => 'Line', 'color' => '#dbc903', 'data' => $dataSetLinea['data']);
            //$dataMeta["value"] = 0;
            //$dataSetMeta["data"][] = $dataMeta;
        

        $data['dataSource']['chart'] = $chart;
        $data['dataSource']['categories'][]["category"] = $category;
        $data['dataSource']['dataset'][] = $dataSetValues['votos'];
        
        return json_encode($data);
    }

    /**
     *
     *  Grafica General de Estado
     *
     */
    public function getDataChartOfVotoGeneralEstado($estado,$linkValue,$type) {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        $chart["caption"] = $estado;
        $chart["captionFontColor"] = "#e20000";
        $chart["sYAxisName"] = "";
        $chart["sNumberSuffix"] = "";
        $chart["sYAxisMaxValue"] = "0";
        $chart["paletteColors"] = "#e20000,#0075c2,#1aaf5d,#e20000,#f2c500,#f45b00,#8e0000";
        $chart["bgColor"] = "#ffffff";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["bgAlpha"] = "0,0";//Fondo 
        $chart["legendShadow"] = "0";
        $chart["showHoverEffect"] = "0";
        $chart["valueFontColor"] = "#ffffff";
        $chart["valuePosition"] = "ABOVE";
        $chart["rotateValues"] = "0";
        $chart["placeValuesInside"] = "0";
        $chart["divlineColor"] = "#999999";
        $chart["divLineDashed"] = "1";
        $chart["divLineDashLen"] = "1";
        $chart["divLineGapLen"] = "1";
        $chart["canvasBgColor"] = "#ffffff";
        $chart["captionFontSize"] = "20";
        $chart["subcaptionFontSize"] = "14";
        $chart["subcaptionFontBold"] = "0";
        $chart["decimalSeparator"] = ",";
        $chart["thousandSeparator"] = ".";
        $chart["inDecimalSeparator"] = ",";
        $chart["inThousandSeparator"] = ".";
        $chart["decimals"] = "2";
        $chart["formatNumberScale"] = "0";
        $chart["toolTipColor"] = "#ffffff";
        $chart["toolTipBgColor"] = "#000000";
        $chart["toolTipBgAlpha"] = "0";
        $chart["toolTipBorderRadius"] = "2";
        $chart["toolTipPadding"] = "5";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";

        $em = $this->getDoctrine()->getManager();

        $label = $dataLocalidad = array();

        //Carga de Nombres de Labels
        $dataSetLocal["seriesname"] = "Voto Pequiven";
        
        $votoNo = $votoSi = 0;
        
        if ($estado != "OTROS") {
            $resultVal = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstadoData($estado);            
            $votoSi = $resultVal[0]["SUM(votoSI)"];
            $votoNo = $resultVal[0]["SUM(votoNO)"];                            
        }else{
            $otros = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstadoOtros();            
            $votoSi = $otros[0]["SUM(votoSI)"];
            $votoNo = $otros[0]["SUM(votoNO)"];            
        }

        //SI VIENE DE GENERAL
        if ($type == 1) {    
            if ($estado == "OTROS") {
                $votosOneMembersEdo = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstadoMembersEdoOtros($estado);
                $votoSi = $votoSi + $votosOneMembersEdo[0]["SUM(votoSI)"];
                $votoNo = $votoNo + $votosOneMembersEdo[0]["SUM(votoNO)"];                            
            }else{
                $votosOneMembersEdo = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstadoMembersEdo($estado);
                $votoSi = $votoSi + $votosOneMembersEdo[0]["SUM(votoSI)"];
                $votoNo = $votoNo + $votosOneMembersEdo[0]["SUM(votoNO)"];
            }

            $estado = $this->AsignedIdEdo($estado);            
            $link  = $this->generateUrl('pequiven_sip_display_voto_general_estado',array('edo' => $estado,'type' => $type));
        }elseif($type == 2){
            $estado = $this->AsignedIdEdo($estado);
            $link  = $this->generateUrl('pequiven_sip_display_voto_pqv_edo',array('edo' => $estado,'type' => $type));            
        }

            if ($linkValue == 1) {
                $dataLocal["link"] = $link;
                $chart["showvalues"]     = "0";
                $chart["showLegend"] = "0";
                $labelSi = "";
                $labelNo = "";
            }elseif($linkValue == 2){
                $dataLocal["link"] = $this->generateUrl('pequiven_sip_display_voto_localidad');
                $chart["showvalues"]     = "1";
                $chart["showLegend"] = "1";                
                $labelSi = "SI";
                $labelNo = "NO";
            }else{
                $chart["showvalues"]     = "1";
                $chart["showLegend"] = "1";                
                $labelSi = "SI";
                $labelNo = "NO";
            }

            $dataLocal["label"] = $labelSi; //Carga de valores SI               
            $dataLocal["value"] = $votoSi; //Carga de valores SI
            $dataSetLocal["data"][] = $dataLocal; //data SI

            $dataLocal["label"] = $labelNo; //Carga de valores NO               
            $dataLocal["value"] = $votoNo; //Carga de valores NO
            $dataSetLocal["data"][] = $dataLocal; //data NO

        $data['dataSource']['chart'] = $chart;                
        $data['dataSource']['dataset'][] = $dataSetLocal;

        return json_encode($data);                
    }

    /**
     *
     *  Grafica de Votos Municipio Barra
     *
     */
    public function getDataChartOfVotoMcpo($estado, $type, $linkValue) {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        $chart["caption"] = "Municipios";
        $chart["captionFontColor"] = "#e20000";
        $chart["captionFontSize"] = "20";                
        $chart["palette"]        = "1";
        $chart["showvalues"]     = "1";
        $chart["paletteColors"]  = "#0075c2,#c90606,#f2c500,#12a830,#1aaf5d";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["yaxisvaluespadding"] = "10";
        $chart["valueFontColor"] = "#ffffff";
        $chart["rotateValues"]   = "1";
        $chart["bgAlpha"] = "0,0";//Fondo         
        $chart["theme"]          = "fint";
        $chart["showborder"]     = "0";
        $chart["decimals"]       = "0";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";
        $chart["baseFontColor"] = "#ffffff";        
        $chart["outCnvBaseFontColor"] = "#ffffff";
        $chart["formatNumberScale"] = "0";

        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["legendItemFontColor"] = "#ffffff";
        $chart["baseFontColor"] = "#ffffff";
        $chart["legendItemFontColor"] = "#ffffff";
        
        $chart["divLineDashed"] = "0";
        $chart["showHoverEffect"] = "1";
        $chart["valuePosition"] = "ABOVE";
        $chart["dashed"] = "0";
        $chart["divLineDashLen"] = "0";
        $chart["divLineGapLen"] = "0";
        $chart["canvasBgAlpha"] = "0,0";
        $chart["toolTipBgColor"] = "#000000";
        //$chart["visible"] = "0";
        //$chart["labelDisplay"] = "ROTATE";

        $em = $this->getDoctrine()->getManager();
        $estadoDescription = $estado;
        $estado = $this->AsignedIdEdo($estado);//Id Estado

        $label = $dataPlan = $dataReal = array();
        //Carga de Nombres de Labels
        $dataSetReal["seriesname"] = "Real";
        $dataSetPlan["seriesname"] = "Plan";
        
        $mcpo = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByMunicipios($estado);            
        $cantMcpo = count($mcpo);
        $cont = $municipio = 0;

        foreach ($mcpo as $key => $value) {
            //Municipio Para consulta
            $municipio = $mcpo[$cont]["descriptionMunicipio"];
            $linea = $municipio;
            $label["label"] = $linea;     
            $category[] = $label;
            
            //Votos por Municipio
            $mcpoVoto = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByVotosMunicipios($municipio, $estadoDescription);            
            $dataRealVoto = $mcpoVoto[0]["SUM(votoSI)"];
            $dataPlanVoto = $mcpoVoto[0]["SUM(votoNO)"];

            $muncpo = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByMcpoId($municipio, $estado);            
            $muncpo = $muncpo[0]["id"];            
            
            if ($type == 1) {
                $mcpoVotoGeneral = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByVotosMunicipiosGeneral($municipio, $estadoDescription);            
                $dataRealVoto = $dataRealVoto + $mcpoVotoGeneral[0]["SUM(votoSI)"];
                $dataPlanVoto = $dataPlanVoto + $mcpoVotoGeneral[0]["SUM(votoNO)"];                                

                $link = $this->generateUrl('pequiven_sip_display_voto_general_mcpo',array('edo' => $estado, 'type' => $type, 'mcpo' => $muncpo));            
            }elseif($type == 2){        
                $link = $this->generateUrl('pequiven_sip_display_voto_pqv_mcpo',array('edo' => $estado, 'type' => $type, 'mcpo' => $muncpo));
            }
                
            if ($linkValue == 1) {
                $dataReal["link"]  = $link;
                $chart["showvalues"] = "1";
            }else{
                $chart["showvalues"] = "0";
            }
            
            $dataPlan["value"] = $dataPlanVoto + $dataRealVoto;
            $dataSetPlan["data"][] = $dataPlan; //data Plan

            $dataReal["value"] = $dataRealVoto;
            $dataSetReal["data"][] = $dataReal; //data Real
            
            $cont++;        
        }
        if ($estado == 30) {
            $name = "OTROS";
            $chart["caption"] = $name;
            $label["label"] = $name;     
            $category[] = $label;

            $otros = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByMunicipioOtros();
            $votoSi = $otros[1]["Cant"];
            $votoNo = $otros[0]["Cant"];

            $dataPlan["value"] = $votoSi + $votoNo;
            $dataSetPlan["data"][] = $dataPlan; //data Plan

            $dataReal["value"] = $votoSi;
            $dataSetReal["data"][] = $dataReal; //data Real
        }

        $data['dataSource']['chart'] = $chart;
        $data['dataSource']['categories'][]["category"] = $category;
        //$data['dataSource']['dataset'][] = $dataSetValues['votos'];
        $data['dataSource']['dataset'][] = $dataSetPlan;
        $data['dataSource']['dataset'][] = $dataSetReal;

        return json_encode($data);
    }

    /**
     *
     *  Grafica Parroquia
     *
     */
    public function getDataChartOfVotoGeneralParroquia($estado ,$mcpo ,$linkValue, $type) {

        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        $chart["captionFontColor"] = "#e20000";
        $chart["sYAxisName"] = "";
        $chart["sNumberSuffix"] = "";
        $chart["sYAxisMaxValue"] = "0";
        $chart["paletteColors"] = "#e20000,#0075c2,#1aaf5d,#e20000,#f2c500,#f45b00,#8e0000";
        $chart["bgColor"] = "#ffffff";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["bgAlpha"] = "0,0";//Fondo 
        $chart["legendShadow"] = "0";
        $chart["showHoverEffect"] = "0";
        $chart["valueFontColor"] = "#ffffff";
        $chart["valuePosition"] = "ABOVE";
        $chart["rotateValues"] = "0";
        $chart["placeValuesInside"] = "0";
        $chart["divlineColor"] = "#999999";
        $chart["divLineDashed"] = "1";
        $chart["divLineDashLen"] = "1";
        $chart["divLineGapLen"] = "1";
        $chart["canvasBgColor"] = "#ffffff";
        $chart["captionFontSize"] = "20";
        $chart["subcaptionFontSize"] = "14";
        $chart["subcaptionFontBold"] = "0";
        $chart["decimalSeparator"] = ",";
        $chart["thousandSeparator"] = ".";
        $chart["inDecimalSeparator"] = ",";
        $chart["inThousandSeparator"] = ".";
        $chart["decimals"] = "2";
        $chart["formatNumberScale"] = "0";
        $chart["toolTipColor"] = "#ffffff";
        $chart["toolTipBgColor"] = "#000000";
        $chart["toolTipBgAlpha"] = "0";
        $chart["toolTipBorderRadius"] = "2";
        $chart["toolTipPadding"] = "5";
        $chart["showLegend"] = "0";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";

        $em = $this->getDoctrine()->getManager();

        $label = $dataLocalidad = array();

        //Carga de Nombres de Labels
        $dataSetLocal["seriesname"] = "Voto Pequiven";

        //Personal PQV por centro         
        $votoNo = $votoSi = 0;
        $edoMcpo = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByMcpoAndEdoId($estado, $mcpo);            
        $estado = $edoMcpo[0]["edo"];
        $mcpo = $edoMcpo[0]["mcpo"];

        if ($estado != 30) {            
            $mcpoVoto = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByVotosMunicipiosId($estado, $mcpo);            
            $votoSI = $mcpoVoto[0]["SUM(votoSI)"];
            $votoNO = $mcpoVoto[0]["SUM(votoNO)"];            
        }else{
            $otros = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByEstadoOtros();
            $votoSI = $otros[1]["Cant"];
            $votoNO = $otros[0]["Cant"];
        }

        if ($type == 1) {            
            $mcpoVotoGeneral = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByVotosMunicipiosIdGeneral($estado, $mcpo);            
            $votoSI = $votoSI + $mcpoVotoGeneral[0]["SUM(votoSI)"];
            $votoNO = $votoNO + $mcpoVotoGeneral[0]["SUM(votoNO)"];            
        }

            $chart["caption"] = $mcpo;
            $label = "";
            $dataLocal["label"] = $label; //Carga de valores                
            $dataLocal["value"] = $votoSI; //Carga de valores
            if ($linkValue == 1) {
            $dataLocal["link"]  = $this->generateUrl('pequiven_sip_display_voto_general_estado',array('edo' => $estado));
                $chart["showvalues"] = "0";
            }else{
                $chart["showvalues"] = "1";
            }

            $dataSetLocal["data"][] = $dataLocal; //data 

            $label = "";
            $dataLocal["label"] = $label; //Carga de valores                
            $dataLocal["value"] = $votoNO; //Carga de valores
            $dataSetLocal["data"][] = $dataLocal; //data 

        $data['dataSource']['chart'] = $chart;                
        $data['dataSource']['dataset'][] = $dataSetLocal;

        return json_encode($data);        
    }

    /**
     *
     *  Grafica de Votos Parroquia Barra
     *
     */
    public function getDataChartOfVotoParroquiaData($estado, $mcpo, $linkValue, $type ) {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        $chart["caption"] = "Parroquias";
        $chart["captionFontColor"] = "#e20000";
        $chart["captionFontSize"] = "20";                
        $chart["palette"]        = "1";
        $chart["showvalues"]     = "1";
        $chart["paletteColors"]  = "#0075c2,#c90606,#f2c500,#12a830,#1aaf5d";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["yaxisvaluespadding"] = "10";
        $chart["valueFontColor"] = "#ffffff";
        $chart["rotateValues"]   = "1";
        $chart["bgAlpha"] = "0,0";//Fondo         
        $chart["theme"]          = "fint";
        $chart["showborder"]     = "0";
        $chart["decimals"]       = "0";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";
        $chart["baseFontColor"] = "#ffffff";        
        $chart["outCnvBaseFontColor"] = "#ffffff";
        $chart["formatNumberScale"] = "0";

        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["legendItemFontColor"] = "#ffffff";
        $chart["baseFontColor"] = "#ffffff";
        $chart["legendItemFontColor"] = "#ffffff";
        
        $chart["divLineDashed"] = "0";
        $chart["showHoverEffect"] = "1";
        $chart["valuePosition"] = "ABOVE";
        $chart["dashed"] = "0";
        $chart["divLineDashLen"] = "0";
        $chart["divLineGapLen"] = "0";
        $chart["canvasBgAlpha"] = "0,0";
        $chart["toolTipBgColor"] = "#000000";

        $em = $this->getDoctrine()->getManager();        
        $estado = $this->AsignedIdEdo($estado);//Id Estado

        $label = $dataPlan = $dataReal = array();
        //Carga de Nombres de Labels
        $dataSetReal["seriesname"] = "Real";
        $dataSetPlan["seriesname"] = "Plan";
        
        $parroq = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByParroquias($estado, $mcpo);                    
        $cantParroq = count($parroq);
        $cont = 0;

        foreach ($parroq as $key => $value) {
            //Municipio Para consulta
            $parroquia = $parroq[$cont]["descriptionParroquia"];
            $linea = $parroquia;
            $label["label"] = $linea;     
            $category[] = $label;
            
            $estado = $this->AsignedDescriptionEdo($estado);
            //Votos por Parroquia
            $parroqVoto = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByVotosParroquia($parroquia, $estado);            
            /*if(isset($parroqVoto)){
                $votoSI = $parroqVoto[0]["SUM(votoSI)"];
                $votoNO = $parroqVoto[0]["SUM(votoNO)"];
            }else{*/
                $votoSI = 0;
                $votoNO = 0;
            //}

            
            if ($type == 1) {
                $parroqGeneral = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByVotosParroquiaGeneral($parroquia, $estado);            
                $votoSI = $votoSI + $parroqGeneral[0]["SUM(votoSI)"];
                $votoNO = $votoNO + $parroqGeneral[0]["SUM(votoNO)"];    

                //$link = $this->generateUrl('pequiven_sip_display_voto_general_mcpo',array('edo' => $estado, 'type' => $type, 'mcpo' => $muncpo));            
            }elseif($type == 2){        
                //$link = $this->generateUrl('pequiven_sip_display_voto_pqv_mcpo',array('edo' => $estado, 'type' => $type, 'mcpo' => $muncpo));
            }
                
            /*if ($linkValue == 1) {
                $dataReal["link"]  = $link;
                $chart["showvalues"] = "1";
            }else{
                $chart["showvalues"] = "0";
            }*/
            
            $dataPlan["value"] = $votoSI + $votoNO;
            $dataSetPlan["data"][] = $dataPlan; //data Plan

            $dataReal["value"] = $votoSI;
            $dataSetReal["data"][] = $dataReal; //data Real
            
            $cont++;       
        }
        if ($estado == 30) {
            $name = "OTROS";
            $chart["caption"] = $name;
            $label["label"] = $name;     
            $category[] = $label;

            $otros = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByMunicipioOtros();
            $votoSi = $otros[1]["Cant"];
            $votoNo = $otros[0]["Cant"];

            $dataPlan["value"] = $votoSI + $votoNO;
            $dataSetPlan["data"][] = $dataPlan; //data Plan

            $dataReal["value"] = $votoSI;
            $dataSetReal["data"][] = $dataReal; //data Real
        }

        $data['dataSource']['chart'] = $chart;
        $data['dataSource']['categories'][]["category"] = $category;
        //$data['dataSource']['dataset'][] = $dataSetValues['votos'];
        $data['dataSource']['dataset'][] = $dataSetPlan;
        $data['dataSource']['dataset'][] = $dataSetReal;

        return json_encode($data);
    }

    /**
     *
     *  Grafica de Voto Localidad
     *
     */
    public function getDataChartOfLocalidad($localidad) {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        $chart["caption"] = $localidad;
        $chart["captionFontColor"] = "#e20000";
        $chart["sYAxisName"] = "";
        $chart["sNumberSuffix"] = "";
        $chart["sYAxisMaxValue"] = "100";
        $chart["paletteColors"] = "#e20000,#0075c2,#1aaf5d,#e20000,#f2c500,#f45b00,#8e0000";
        $chart["bgColor"] = "#ffffff";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["bgAlpha"] = "0,0";//Fondo 
        $chart["legendShadow"] = "0";
        $chart["showHoverEffect"] = "0";
        $chart["valueFontColor"] = "#ffffff";
        $chart["valuePosition"] = "ABOVE";
        $chart["rotateValues"] = "1";
        $chart["placeValuesInside"] = "0";
        $chart["divlineColor"] = "#999999";
        $chart["divLineDashed"] = "1";
        $chart["divLineDashLen"] = "1";
        $chart["divLineGapLen"] = "1";
        $chart["canvasBgColor"] = "#ffffff";
        $chart["captionFontSize"] = "20";
        $chart["subcaptionFontSize"] = "14";
        $chart["subcaptionFontBold"] = "0";
        $chart["decimalSeparator"] = ",";
        $chart["thousandSeparator"] = ".";
        $chart["inDecimalSeparator"] = ",";
        $chart["inThousandSeparator"] = ".";
        $chart["decimals"] = "2";
        $chart["formatNumberScale"] = "0";
        $chart["toolTipColor"] = "#ffffff";
        $chart["toolTipBgColor"] = "#000000";
        $chart["toolTipBgAlpha"] = "0";
        $chart["toolTipBorderRadius"] = "2";
        $chart["toolTipPadding"] = "5";
        $chart["showLegend"] = "1";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";

        $em = $this->getDoctrine()->getManager();

        $label = $dataLocalidad = array();
        
        //Carga de Nombres de Labels
        $dataSetLocal["seriesname"] = "Voto Pequiven";        
        
        $resultLocalidad = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByLocalidad($localidad);
        $votoNO = $votoSI = 0;

        $votoSI = $resultLocalidad[0]["SUM(votoSI)"];
        $votoNO = $resultLocalidad[0]["SUM(votoNO)"];

        $label = "SI";
        $dataLocal["label"] = $label; //Carga de valores                
        $dataLocal["value"] = $votoSI; //Carga de valores
        $dataSetLocal["data"][] = $dataLocal; //data 

        $label = "NO";
        $dataLocal["label"] = $label; //Carga de valores                
        $dataLocal["value"] = $votoNO; //Carga de valores
        $dataSetLocal["data"][] = $dataLocal; //data 
        

        $data['dataSource']['chart'] = $chart;                
        $data['dataSource']['dataset'][] = $dataSetLocal;

        return json_encode($data);        
    }

    /**
     *
     *  Grafica de Votos Parroquia Barra
     *
     */
    public function getDataChartOfLocalidadBar($localidad) {
        
        $data = array(
            'dataSource' => array(
                'chart' => array(),
                'categories' => array(
                ),
                'dataset' => array(
                ),
            ),
        );
        $chart = array();

        //$chart["caption"] = $localidad;
        $chart["captionFontColor"] = "#e20000";
        $chart["captionFontSize"] = "20";                
        $chart["palette"]        = "1";
        $chart["showvalues"]     = "1";
        $chart["paletteColors"]  = "#0075c2,#c90606,#f2c500,#12a830,#1aaf5d";
        $chart["showBorder"] = "0";
        $chart["showCanvasBorder"] = "0";
        $chart["yaxisvaluespadding"] = "10";
        $chart["valueFontColor"] = "#ffffff";
        $chart["rotateValues"]   = "1";
        $chart["bgAlpha"] = "0,0";//Fondo         
        $chart["theme"]          = "fint";
        $chart["showborder"]     = "0";
        $chart["decimals"]       = "0";
        $chart["showLegend"] = "0";
        $chart["legendBgColor"] = "#ffffff";
        $chart["legendItemFontSize"] = "10";
        $chart["legendItemFontColor"] = "#666666";
        $chart["baseFontColor"] = "#ffffff";        
        $chart["outCnvBaseFontColor"] = "#ffffff";
        $chart["formatNumberScale"] = "0";

        $chart["usePlotGradientColor"] = "0";
        $chart["plotBorderAlpha"] = "10";
        $chart["legendBorderAlpha"] = "0";
        $chart["legendBgAlpha"] = "0";
        $chart["legendItemFontColor"] = "#ffffff";
        $chart["baseFontColor"] = "#ffffff";
        $chart["legendItemFontColor"] = "#ffffff";
        
        $chart["divLineDashed"] = "0";
        $chart["showHoverEffect"] = "1";
        $chart["valuePosition"] = "ABOVE";
        $chart["dashed"] = "0";
        $chart["divLineDashLen"] = "0";
        $chart["divLineGapLen"] = "0";
        $chart["canvasBgAlpha"] = "0,0";
        $chart["toolTipBgColor"] = "#000000";

        $em = $this->getDoctrine()->getManager();
        
        $label = $dataPlan = $dataReal = array();
        
        
        $resultLocalidad = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByLocalidad($localidad);
        $votoNO = $votoSI = 0;

        $votoSI = $resultLocalidad[0]["SUM(votoSI)"];
        $votoNO = $resultLocalidad[0]["SUM(votoNO)"];

        //votos
        $label["label"] = "Votos PQV";     
        $category[] = $label;        
        
        $dataReal["value"] = $votoSI; //Carga de valores General
        $dataSetReal["data"][] = $dataReal; //data                 
        $dataPlan["value"] = $votoNO + $votoSI; //Carga de valores General
        $dataSetPlan["data"][] = $dataPlan; //data 


        //1x10
        $resultLocalidad = $em->getRepository("\Pequiven\SEIPBundle\Entity\Sip\Centro")->findByLocalidad1x10($localidad);
        $votoSI = $resultLocalidad[0]["SUM(votoSI)"];
        $votoNO = $resultLocalidad[0]["SUM(votoNO)"];

        $label["label"] = "1x10";     
        $category[] = $label;
        
        $dataReal["value"] = 100; //Carga de valores General
        $dataSetReal["data"][] = $dataReal; //data                 
        $dataPlan["value"] = 50; //Carga de valores General
        $dataSetPlan["data"][] = $dataPlan; //data 

        $dataSetReal["seriesname"] = "Real";                
        $dataSetPlan["seriesname"] = "Plan";                

        $data['dataSource']['chart'] = $chart;
        $data['dataSource']['categories'][]["category"] = $category;
        $data['dataSource']['dataset'][] = $dataSetPlan;
        $data['dataSource']['dataset'][] = $dataSetReal;

        return json_encode($data);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param bool|string    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws LogicException If DoctrineBundle is not available
     */
    public function getDoctrine() {
        if (!$this->container->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }
}
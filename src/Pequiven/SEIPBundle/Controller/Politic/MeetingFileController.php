<?php

namespace Pequiven\SEIPBundle\Controller\Politic;

use DateTime;
use Pequiven\SEIPBundle\Controller\SEIPController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controlador de reuniones de estudio de trabajo
 * @author Victor Tortolero vart10.30@gmail.com
 */
class MeetingFileController extends SEIPController {

    function listAction(Request $request) {
        
        $criteria = $request->get('filter', $this->config->getCriteria());
        $sorting = $request->get('sorting', $this->config->getSorting());
        $repository = $this->getRepository();

        if ($this->config->isPaginated()) {
            $resources = $this->resourceResolver->getResource(
                    $repository, 'createPaginatorMeetingFile', array($criteria, $sorting)
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
        );
        $apiDataUrl = $this->generateUrl('pequiven_work_study_circle_document_list', $routeParameters);
        
        
        
        $view = $this
                ->view()
                ->setTemplate($this->config->getTemplate('listFiles.html'))
                //->setTemplate("PequivenSEIPBundle:Politic:Meeting/listFiles.html.twig")
                ->setTemplateVar($this->config->getPluralResourceName())
        ;
        
        $view->getSerializationContext()->setGroups(array('id', 'api_list','nameFile'));
        if ($request->get('_format') == 'html') {

            $data = array(
                'apiDataUrl' => $apiDataUrl,
            );
            $view->setData($data);
        } else {
            $formatData = $request->get('_formatData', 'default');

            $view->setData($resources->toArray('', array(), $formatData));
        }
        
        return $this->handleView($view);
        
        
    }

}

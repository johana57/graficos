<?php

namespace Pequiven\SEIPBundle\Controller\Politic;

use DateTime;
use Pequiven\SEIPBundle\Entity\Politic\WorkStudyCircle;
use Pequiven\SEIPBundle\Controller\SEIPController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Pequiven\SEIPBundle\Form\Politic\WorkStudyCircleType;

/**
 * Controlador del círculo de estudio de trabajo
 *
 */
class WorkStudyCircleController extends SEIPController {

    public function editWorkStudyCircleMemberAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $idUser = $request->get('idUser');

        $user = $em->getRepository('PequivenSEIPBundle:User')->find($idUser);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find ArrangementProgram entity.');
        }

        $formUser = $this->createForm(new \Pequiven\SEIPBundle\Form\User\UserType(), $user);
        $formUser->handleRequest($request);

        $em->getConnection()->beginTransaction();
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $em->persist($user);

            try {
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            $this->get('session')->getFlashBag()->add('success', 'Miembro actualizado correctamente');
            return $this->redirect($this->generateUrl('pequiven_work_study_circle_show', array("id" => $user->getWorkStudyCircle()->getId())));
        }

        return $this->render('PequivenSEIPBundle:Politic:WorkStudyCircle\editMember.html.twig', array(
                    'user' => $user,
                    'form_user' => $formUser->createView()
        ));
    }

    public function createAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $workStudyCircle = new WorkStudyCircle();
        $form = $this->createForm(new WorkStudyCircleType, $workStudyCircle);
        $form->handleRequest($request);

        $userData = new \Pequiven\SEIPBundle\Entity\User();
        $formUser = $this->createForm(new \Pequiven\SEIPBundle\Form\User\UserType(), $userData);
        $formUser->handleRequest($request);

        $user = $this->getUser();
        //$period = $this->getPeriodService();
        $em->getConnection()->beginTransaction();
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
//            var_dump($data);
//            die();

            $workStudyCircle->setCreatedBy($user);
            $workStudyCircle->setPeriod($period = $this->getPeriodService()->getPeriodActive());
            $workStudyCircle->setCodigo($this->setNewRef($request->get("workStudyCircle_data")["complejo"]));
            //die("Formulario enviado correctamente");

            $em->persist($workStudyCircle);
            $em->flush();

            $user->setCellphone($request->get("userType_data")["cellphone"]);
            $user->setIndentification($request->get("userType_data")["indentification"]);
            $user->setExt($request->get("userType_data")["ext"]);


            try {
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }

            $workStudyCircle = $em->getRepository('PequivenSEIPBundle:Politic\WorkStudyCircle')->findOneBy(array('createdBy' => $user->getId()));
            $this->addWorkStudyCircleToUser($workStudyCircle, $request->get("workStudyCircle_data")["userWorkerId"]);
            $this->addWorkStudyCircleToUser($workStudyCircle, array($user->getId()));

            $this->get('session')->getFlashBag()->add('success', 'Círculo de Estudio guardado correctamente');
            //return $this->redirect($this->generateUrl('pequiven_seip_default_index'));
            return $this->redirect($this->generateUrl('pequiven_work_study_circle_show', array("id" => $workStudyCircle->getId())));

            //return $this->redirect($this->generateUrl('saci_people_list', array('id' => $people->getId())));
        }

        return $this->render('PequivenSEIPBundle:Politic:WorkStudyCircle\create.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'form_user' => $formUser->createView()
        ));
    }

    public function addWorkStudyCircleToUser(WorkStudyCircle $workStudyCircle, $members = array()) {
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
        foreach ($members as $member) {
            $user = $em->getRepository('PequivenSEIPBundle:User')->findOneBy(array('id' => $member));
            $user->setWorkStudyCircle($workStudyCircle);
            $em->persist($user);
        }

        $user = $this->getUser();
        $user->setWorkStudyCircle($workStudyCircle);
        $em->persist($user);

        try {
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

        return true;
    }

    public function setNewRef($location) {
        $em = $this->getDoctrine()->getManager();
        $complejo = $em->getRepository('PequivenMasterBundle:Complejo')->findOneBy(array('id' => $location));
        $workStudyCircles = $em->getRepository('PequivenSEIPBundle:Politic\WorkStudyCircle')->findBy(array('complejo' => $location));
        $totalWorkStudyCircles = count($workStudyCircles);

        $ref = 'CET-' . $complejo->getRef() . '-';
        $contRef = $totalWorkStudyCircles + 1;
        if ($totalWorkStudyCircles < 10) {
            $ref = $ref . '000' . $contRef;
        } elseif ($totalWorkStudyCircles >= 10 && $totalWorkStudyCircles < 100) {
            $ref = $ref . '00' . $contRef;
        } elseif ($totalWorkStudyCircles >= 100 && $totalWorkStudyCircles < 1000) {
            $ref = $ref . '0' . $contRef;
        } elseif ($totalWorkStudyCircles >= 1000) {
            $ref = $ref . $contRef;
        }

        return $ref;
    }

    public function showAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $workStudyCircle = $em->getRepository('PequivenSEIPBundle:Politic\WorkStudyCircle')->findOneBy(array('id' => $request->get("id")));

        return $this->render('PequivenSEIPBundle:Politic:WorkStudyCircle\show.html.twig', array(
                    'workStudyCircle' => $workStudyCircle
        ));
    }

    protected function getPeriodService() {
        return $this->container->get('pequiven_seip.service.period');
    }

    public function addOthersMembersAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $workStudyCircle = new WorkStudyCircle();
        $form = $this->createForm(new WorkStudyCircleType, $workStudyCircle);
        $form->handleRequest($request);

        $workStudyCircleRepo = $em->getRepository('PequivenSEIPBundle:Politic\WorkStudyCircle')->findOneBy(array('id' => $request->get("idWorkStudyCircle")));

        if ($form->isSubmitted()) {

            $this->addWorkStudyCircleToUser($workStudyCircleRepo, $request->get("workStudyCircle_data")["userWorkerId"]);


            $this->get('session')->getFlashBag()->add('success', 'Nuevos miembros han sido agregados con éxito ');
            //return $this->redirect($this->generateUrl('pequiven_seip_default_index'));
            return $this->redirect($this->generateUrl('pequiven_work_study_circle_show', array("id" => $request->get("idWorkStudyCircle"))));
        }

        return $this->render('PequivenSEIPBundle:Politic:WorkStudyCircle\addOthersMembers.html.twig', array(
                    'id' => $request->get("idWorkStudyCircle"),
                    'form' => $form->createView()
        ));
    }

}

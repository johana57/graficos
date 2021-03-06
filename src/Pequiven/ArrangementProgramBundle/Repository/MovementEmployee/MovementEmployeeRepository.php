<?php

namespace Pequiven\ArrangementProgramBundle\Repository\MovementEmployee;

use Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram;
use Pequiven\SEIPBundle\Entity\Period;
use Pequiven\SEIPBundle\Entity\User;
use Pequiven\ArrangementProgramBundle\Entity\Goal;
use Pequiven\SEIPBundle\Doctrine\ORM\SeipEntityRepository as EntityRepository;

/**
 * Repositorio de MOVIMIENTO DE EMPLEADOS
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MovementEmployeeRepository extends EntityRepository {

    function FindMovementDetailsbyGoal($idGoal) {
        $qb = $this->getQueryBuilder();
        $qb
                ->Select('mov')
                ->andWhere('mov.id_affected= :goals')
                ->andWhere('mov.typeMov= :type')
                ->orderBy('mov.date')
                ->setParameter('goals', $idGoal)
                ->setParameter('type', "Goal")

        ;
        return $qb->getQuery()->getResult();
    }

    function FindMovementDetailsbyGoalbyUser($idGoal, $idUser) {
        $qb = $this->getQueryBuilder();
        $qb
                ->Select('mov')
                ->andWhere('mov.id_affected= :goals')
                ->andWhere('mov.typeMov= :type')
                ->andWhere('mov.User= :user')
                ->orderBy('mov.date')
                ->setParameter('goals', $idGoal)
                ->setParameter('user', $idUser)
                ->setParameter('type', "Goal")
        ;
        return $qb->getQuery()->getResult();
    }

    function FindMovementDetailsbyAP($idAP) {
        $qb = $this->getQueryBuilder();
        $qb
                ->Select('mov')
                ->andWhere('mov.id_affected= :AP')
                ->andWhere('mov.typeMov= :type')
                ->orderBy('mov.date')
                ->setParameter('AP', $idAP)
                ->setParameter('type', "AP")

        ;
        return $qb->getQuery()->getResult();
    }

    function FindMovementDetailsbyAPbyUser($idAP, $idUser) {
        $qb = $this->getQueryBuilder();
        $qb
                ->Select('mov')
                ->andWhere('mov.id_affected= :AP')
                ->andWhere('mov.typeMov= :type')
                ->andWhere('mov.User= :user')
                ->orderBy('mov.date')
                ->setParameter('AP', $idAP)
                ->setParameter('user', $idUser)
                ->setParameter('type', "AP")
        ;
        return $qb->getQuery()->getResult();
    }

    public function getMovementHistory($idAP) {

        $em = $this->getEntityManager();
        $db = $em->getConnection();

        $sql = '
SELECT (SELECT CONCAT(COALESCE(firstname, "")," ",COALESCE(lastname, "")) FROM seip_user WHERE id = ME.user_id) AS user,
    ME.typeMov, ME.id_affected,    ME.type,    ME.cause,    ME.date,    ME.realAdvance,    ME.planned,    ME.observations
FROM
    MovementEmployee AS ME
WHERE
    ME.deletedAt IS NULL AND (ME.id_Affected = ' . $idAP . ' OR ME.id_Affected IN
    (SELECT id FROM Goal WHERE timeline_id IN (SELECT timeline_id FROM ArrangementProgram WHERE id = ' . $idAP . ')))
';

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    function getAlias() {
        return 'mov';
    }

}

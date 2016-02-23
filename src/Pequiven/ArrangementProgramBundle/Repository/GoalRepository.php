<?php

namespace Pequiven\ArrangementProgramBundle\Repository;

use Pequiven\SEIPBundle\Entity\Period;
use Pequiven\SEIPBundle\Entity\User;
use Tecnocreaciones\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * GoalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GoalRepository extends EntityRepository {

    /**
     * Retorna las metas del usuario en los programa de gestion donde no es responsable
     * @param User $user
     * @param Period $period
     * @param array $criteria
     * @return type
     */
    function findGoalsByUserAndPeriod(User $user, Period $period, array $criteria = array()) {
        $qb = $this->getQueryBuilder();
        $qb
                ->innerJoin('g.timeline', 't')
                ->innerJoin('g.responsibles', 'g_r')
                ->innerJoin('t.arrangementProgram', 'ap')
                ->innerJoin('ap.responsibles', 'ap_r')
                ->andWhere('ap_r.id != :responsible')
                ->andWhere('ap.period = :period')
                ->andWhere('g_r.id = :responsible')
                ->andWhere('ap.status != :status')
                ->setParameter('period', $period)
                ->setParameter('responsible', $user)
                ->setParameter('status', \Pequiven\ArrangementProgramBundle\Entity\ArrangementProgram::STATUS_REJECTED)
        ;
        if (isset($criteria['notArrangementProgram'])) {
            $qb->andWhere('ap.id != :arrangementProgram');
            $qb->setParameter('arrangementProgram', $criteria['notArrangementProgram']);
        }
        return $qb->getQuery()->getResult();
    }

    function verificationGoalUser(User $user, $Goal, Period $period) {

        $qb = $this->getQueryBuilder();
        $qb
                ->innerJoin('g.responsibles', 'resp')
                ->andWhere('g.period = :per')
                ->andWhere('g.id = :ide')
                ->andWhere('resp.id = :responsible')
                ->setParameter('responsible', $user)
                ->setParameter('per', $period)
                ->setParameter('ide', $Goal)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * RETORNA LOS ID DE LAS METAS EN LAS CUALES FUE DESINCORPORADO
     * @param type $user
     * @param type $goal
     * @param type $period
     */
    function getDivestedIdGoalsbyUser($user, $period) {

        $em = $this->getEntityManager();
        $db = $em->getConnection();

        $sql = 'SELECT mov.id_affected FROM  MovementEmployee AS mov
            WHERE  mov.typeMov = \'Goal\' AND mov.User_id = ' . $user . ' AND mov.period_id = ' . $period . '
                AND mov.id_affected NOT IN (SELECT goal_id FROM goals_users WHERE user_id = ' . $user . ')'
        ;

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * FUNCION QUE RETORNA LOS RESPONSABLES DE METAS EN UN PROGRAMA DE GESTIÓN. LO UTILIZO PARA EL SENCHA DE NOTIFICACION DE PG
     * @param type $idAP
     * @return type
     */
    function getGoalResponsiblesbyAP($idAP) {
        $em = $this->getEntityManager();
        $db = $em->getConnection();

        $sql = '
            SELECT DISTINCT
    user.id,
    (CONCAT(COALESCE(user.firstname, " "),
            " ",
            COALESCE(user.lastname, " "))) AS Responsable
FROM
    goals_users AS g_u
        INNER JOIN
    seip_user AS user ON (user.id = g_u.user_id)
        INNER JOIN
    Goal AS goal ON (goal.id = g_u.goal_id)
        INNER JOIN
    ArrangementProgram AS ap ON (goal.timeline_id = ap.timeline_id)
WHERE
    ap.id = ' . $idAP . '
            '
        ;

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    protected function getAlias() {
        return 'g';
    }

}

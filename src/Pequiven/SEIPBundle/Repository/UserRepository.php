<?php

namespace Pequiven\SEIPBundle\Repository;

use Pequiven\SEIPBundle\Entity\User;
use Tecnocreaciones\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Repositorio del usuario
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * Retornar los usuario a los cuales le puedo asignar programas de gestion tacticos
     * @return type
     */
    function findQueryToAssingTacticArrangementProgram(){
        $qb = $this->getQueryBuilder();
        $user = $this->getUser();
        $groups = $user->getGroups();
        $group = $groups[0];
        $level = $group->getLevel();
        $qb
            ->innerJoin('u.groups','g')
            ->andWhere($qb->expr()->orX('g.level <= :level','u.id = :user'))
            ->andWhere('g.typeRol = :typeRol')
            ->andWhere('u.gerencia = :gerencia')
            ->setParameter('level', $level)
            ->setParameter('user', $user)
            ->setParameter('typeRol', \Pequiven\MasterBundle\Entity\Rol::TYPE_ROL_OWNER)
            ->setParameter('gerencia', $user->getGerencia())
            ;
        return $qb;
    }
    
    /**
     * Retornar los usuario a los cuales le puedo asignar metas de un programa de gestion tactico
     * @return type
     */
    function findQueryToAssingTacticArrangementProgramGoal(array $users){
        $qb = $this->getQueryBuilder();
        $level = 0;
        $usersId = array();
        foreach ($users as $user) {
            $groups = $user->getGroups();
            foreach ($groups as $group) {
                if($group->getLevel() > $level){
                    $level = $group->getLevel();
                }
            }
            $usersId[] = $user->getId();
        }
        $qb
            ->innerJoin('u.groups','g')
            ->andWhere($qb->expr()->orX('g.level <= :level',$qb->expr()->in('u.id', $usersId)))
            ->andWhere('u.gerencia = :gerencia')
            ->andWhere('g.typeRol = :typeRol')
            ->setParameter('level', $level)
            ->setParameter('typeRol', \Pequiven\MasterBundle\Entity\Rol::TYPE_ROL_OWNER)
            ->setParameter('gerencia', $user->getGerencia())
            ;
        return $qb;
    }
    
    /**
     * Retornar los usuario a los cuales le puedo asignar metas de un programa de gestion tactico
     * @return type
     */
    function findToAssingTacticArrangementProgramGoal(array $users){
        return $this->findQueryToAssingTacticArrangementProgramGoal($users)->getQuery()->getResult();
    }
    
    function findUsers(array $responsiblesId) {
        $qb = $this->getQueryBuilder();
        $qb
           ->addSelect('g')
           ->innerJoin('u.groups', 'g')
           ->andWhere($qb->expr()->in('u.id', $responsiblesId))
           ->andWhere('u.enabled = :enabled')
           ->setParameter('enabled', true)
            ;
        return $qb->getQuery()->getResult();
    }
    
    protected function getAlias() {
        return 'u';
    }
}

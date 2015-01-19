<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pequiven\MasterBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tecnocreaciones\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository as baseEntityRepository;
/**
 * Description of GerenciaSecondRepository
 *
 * @author matias
 */
class GerenciaSecondRepository extends baseEntityRepository {
    
    public function getGerenciaSecondOptions($options = array()){
        $data = array();
        
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
                        ->select('gs')
                        ->from('\Pequiven\MasterBundle\Entity\GerenciaSecond', 'gs')
                        ->andWhere('gs.enabled = ' . 1);

        //En caso de que se conozca las
        if(isset($options['gerencias']) && $options['gerencias'] != 0) {
            $query->andWhere("gs.gerencia IN (" . implode(',',$options['gerencias']) . ')');
        }

        $gerencias = $query->getQuery()
                           ->getResult();
        
        foreach($gerencias as $gerencia){
            if(!$gerencia->getGerencia()->getComplejo() && !$gerencia->getGerencia()){
                continue;
            }
            if(!array_key_exists($gerencia->getGerencia()->getComplejo()->getDescription().'-'.$gerencia->getGerencia()->getDescription(), $data)){
                $data[$gerencia->getGerencia()->getComplejo()->getDescription().'-'.$gerencia->getGerencia()->getDescription()] = array();
            }
            
            $data[$gerencia->getGerencia()->getComplejo()->getDescription().'-'.$gerencia->getGerencia()->getDescription()][$gerencia->getId()] = $gerencia;
        }

        return $data;
    }
    
    
     /**
     * Crea un paginador para las gerencias de 2da línea
     * 
     * @param array $criteria
     * @param array $orderBy
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    function createPaginatorGerenciaSecond(array $criteria = null, array $orderBy = null) {
        $queryBuilder = $this->getCollectionQueryBuilder();
        
        $queryBuilder->leftJoin('gs.gerencia', 'g');
        $queryBuilder->leftJoin('gs.complejo', 'c');

        //Filtro gerencia 2da Línea
        if(isset($criteria['gerenciaSecond'])){
            $queryBuilder->andWhere($queryBuilder->expr()->like('gs.description', "'%".$criteria['gerenciaSecond']."%'"));
        }
        //Filtro gerencia 1ra Línea
        if(isset($criteria['gerenciaFirst'])){
            $queryBuilder->andWhere($queryBuilder->expr()->like('g.description', "'%".$criteria['gerenciaFirst']."%'"));
        }
        //Filtro localidad
        if(isset($criteria['complejo'])){
            $queryBuilder->andWhere($queryBuilder->expr()->like('c.description', "'%".$criteria['complejo']."%'"));
        }

//        $this->applyCriteria($queryBuilder, $criteria);
//        $this->applySorting($queryBuilder, $orderBy);
        
        return $this->getPaginator($queryBuilder);
    }
    
    function findGerenciaSecond(array $criteria = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();
        $criteria = new \Doctrine\Common\Collections\ArrayCollection($criteria);
        $criteria->remove('view_planning');
        //Filtro de gerencia de segunda linea modular y vinculante
        if(($typeManagement = $criteria->remove('typeManagement')) != null){
            $complejo = $criteria->remove('complejo');
            $queryBuilder
                        ->andWhere('gs.complejo = :complejo');
            
            if($typeManagement == \Pequiven\MasterBundle\Model\GerenciaSecond::TYPE_MANAGEMENT_MODULAR){
                $queryBuilder
                        ->andWhere('gs.modular = :typeManagement');
            }else{
                $queryBuilder
                        ->andWhere('gs.vinculante = :typeManagement');
            }
            $queryBuilder
                    ->setParameter('typeManagement', true)
                    ->setParameter('complejo', $complejo)
                ;
        }else if(($gerencia = $criteria->remove('gerencia')) != null){
            $queryBuilder
                    ->innerJoin('gs.gerencia', 'g')
                    ->leftJoin('gs.gerenciaVinculants', 'gv')
                    ->andWhere('g.id = :gerencia')
                    ->orWhere('gv.id = :gerencia')
                    ->setParameter('gerencia', $gerencia)
                ;
        }
        return $queryBuilder->getQuery()->getResult();
    }
    
    
    public function findByGerenciaFirst($options = array()){
        $qb = $this->getQueryBuilder();
        
        if(isset($options['gerencia'])){
            $qb->andWhere('gs.gerencia = '.$options['gerencia']);
            $qb->leftJoin('gs.gerenciaVinculants', 'gv');
            $qb->orWhere('gv.id = '.$options['gerencia']);
        }
        
        $qb->andWhere('gs.enabled = :enabled');
        
        $qb->setParameter('enabled', true);
        
        return $qb->getQuery()->getResult();
    }
    
    public function findWithObjetives($id) 
    {
        $qb = $this->getQueryBuilder();
        $qb
            ->addSelect('gs_ob')
            ->addSelect('gs_ob_c')
            ->addSelect('gs_ob_p')
            ->leftJoin('gs.operationalObjectives', 'gs_ob')
            ->leftJoin('gs_ob.childrens', 'gs_ob_c')
            ->leftJoin('gs_ob.parents', 'gs_ob_p')
            ->andWhere('gs.id = :gerencia')
            ->setParameter('gerencia', $id)
                ;
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    protected function getAlias() {
        return 'gs';
    }
}

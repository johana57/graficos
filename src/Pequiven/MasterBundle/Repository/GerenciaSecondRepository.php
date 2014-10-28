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

        if(isset($criteria['description'])){
            $description = $criteria['description'];
            unset($criteria['description']);
            $queryBuilder->andWhere($queryBuilder->expr()->like('o.description', "'%".$description."%'"));
        }
//        if(isset($criteria['rif'])){
//            $rif = $criteria['rif'];
//            unset($criteria['rif']);
//            $queryBuilder->andWhere($queryBuilder->expr()->like('o.rif', "'%".$rif."%'"));
//        }

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);
        
        return $this->getPaginator($queryBuilder);
    }
    
    function findGerenciaSecond(array $criteria = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();
        $criteria = new \Doctrine\Common\Collections\ArrayCollection($criteria);
        
        //Filtro de gerencia de segunda linea modular y vinculante
        if(($typeManagement = $criteria->remove('typeManagement')) != null){
            $complejo = $criteria->remove('complejo');
            $queryBuilder
                        ->andWhere('o.complejo = :complejo');
            
            if($typeManagement == \Pequiven\MasterBundle\Model\GerenciaSecond::TYPE_MANAGEMENT_MODULAR){
                $queryBuilder
                        ->andWhere('o.modular = :typeManagement');
            }else{
                $queryBuilder
                        ->andWhere('o.vinculante = :typeManagement');
            }
            $queryBuilder
                    ->setParameter('typeManagement', true)
                    ->setParameter('complejo', $complejo)
                ;
        }else if(($gerencia = $criteria->remove('gerencia')) != null){
            $queryBuilder
                    ->innerJoin('o.gerencia', 'g')
                    ->andWhere('g.id = :gerencia')
                    ->setParameter('gerencia', $gerencia)
                ;
        }
        return $queryBuilder->getQuery()->getResult();
    }
}

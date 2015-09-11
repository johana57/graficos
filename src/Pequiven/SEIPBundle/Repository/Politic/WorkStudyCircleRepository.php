<?php

namespace Pequiven\SEIPBundle\Repository\Politic;

use Pequiven\SEIPBundle\Entity\Politic\WorkStudyCircle;
use Pequiven\SEIPBundle\Entity\Period;
use Pequiven\SEIPBundle\Entity\User;
use Pequiven\SEIPBundle\Doctrine\ORM\SeipEntityRepository as EntityRepository;

/**
 * Repositorio del Círculo de Estudio de Trabajo
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkStudyCircleRepository extends EntityRepository
{
    
    /**
     * Crea un paginador para los Circulos de Estudio y Trabajo
     * 
     * @param array $criteria
     * @param array $orderBy
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    function createPaginatorByWorkStudyCircle(array $criteria = null, array $orderBy = null) {
        //$criteria['for_view'] = true;
        $orderBy['wsc.name'] = 'ASC';
        return $this->createPaginator($criteria, $orderBy);
    }
    
    protected function getAlias() {
        return 'wsc';
    }

    /**
     * Crea un paginador de los Circulos de Estudios
     * Filtrados por Programas de Gestión
     * @param array $criteria
     * @param array $orderBy
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    function createPaginatorWorkStudy(array $criteria = null, array $orderBy = null) {
        
        //$criteria['for_view_work'] = false;
        $orderBy['wsc.id'] = 'ASC';
        
        return $this->createPaginator($criteria, $orderBy);
    }
}
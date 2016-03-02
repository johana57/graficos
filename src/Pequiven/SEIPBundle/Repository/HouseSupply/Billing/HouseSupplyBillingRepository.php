<?php

namespace Pequiven\SEIPBundle\Repository\HouseSupply\Billing;

use Pequiven\SEIPBundle\Entity\Period;
use Pequiven\SEIPBundle\Entity\User;
use Pequiven\SEIPBundle\Doctrine\ORM\SeipEntityRepository as EntityRepository;

/**
 * Repositorio de MOVIMIENTO DE EMPLEADOS
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HouseSupplyBillingRepository extends EntityRepository {

    function FindNextOrderNro($type) {

        $qb = $this->getQueryBuilder();
        $qb
                ->select('MAX(Bill.nroBill) as nro')
                ->andWhere('Bill.type= :type')
                ->setParameter('type', $type)
        ;

        //print($qb->getQuery()->getSQL());        

        return $qb->getQuery()->getResult();
    }

    function getAlias() {
        return 'Bill';
    }

}

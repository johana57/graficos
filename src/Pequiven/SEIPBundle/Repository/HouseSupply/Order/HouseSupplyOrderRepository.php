<?php

namespace Pequiven\SEIPBundle\Repository\HouseSupply\Order;

use Pequiven\SEIPBundle\Entity\Period;
use Pequiven\SEIPBundle\Entity\User;
use Pequiven\SEIPBundle\Doctrine\ORM\SeipEntityRepository as EntityRepository;

/**
 * Repositorio de Ordenes HouseSupply
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HouseSupplyOrderRepository extends EntityRepository {

    function FindNextOrderNro($type) {

        $qb = $this->getQueryBuilder();
        $qb
                ->select('MAX(HSOrder.nroOrder) as nro')
                ->andWhere('HSOrder.type= :type')
                ->setParameter('type', $type)
        ;

        //return $qb->getQuery()->getResult();
    }

    function DeleteItemOrder($id) {
        $em = $this->getEntityManager();
        $db = $em->getConnection();

        $sql = 'DELETE FROM seip_gsh_order_items WHERE id="' . $id . '"';

        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    function getAlias() {
        return 'HSOrder';
    }

}

<?php

namespace Pequiven\ArrangementProgramBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repositorio de detalle de las metas
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GoalDetailsIndRepository extends EntityRepository {

    protected function getAlias() {
        return 'goalDetailsInd';
    }

}
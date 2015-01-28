<?php

namespace Pequiven\SEIPBundle\Repository;

use Tecnocreaciones\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * PeriodRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PeriodRepository extends EntityRepository
{
    /**
     * Devuelve el periodo activo en el sistema
     * @return type
     */
    function findOneActive()
    {
        return $this->findOneBy(array('status' => true));
    }
    
    /**
     * Devuelve todos los periodos disponibles para consultas de resultados
     * @return type
     */
    function findAllForConsultation()
    {
        return $this->findAll();
    }
    
    protected function getAlias() {
        return "p";
    }
}

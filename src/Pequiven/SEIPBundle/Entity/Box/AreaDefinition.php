<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pequiven\SEIPBundle\Entity\Box;

use Tecnocreaciones\Bundle\BoxBundle\Model\AreaDefinitionInterface;

/**
 * Definicion de las areas del sistema
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class AreaDefinition implements AreaDefinitionInterface
{
    public function getAreas() 
    {
        return array(
            array('name' => 'pequiven_seip.area.main', 'translation_domain' => 'PequivenSEIPBundle'),
            array('name' => 'pequiven_seip.area.events', 'translation_domain' => 'PequivenSEIPBundle'),
        );
    }
}

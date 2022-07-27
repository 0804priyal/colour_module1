<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Model\Entity\Attribute\Backend\Reference;

class Unit extends \HS\BasePrice\Model\Entity\Attribute\Backend\AbstractUnit
{
    /**
     * Retrieve product unit.
     *
     * @return string
     */
    protected function getProductUnitAttributeValue($object)
    {
        return $object->getData('base_price_unit');
    }

    /**
     * Retrieve reference unit.
     *
     * @return string
     */
    protected function getReferenceUnitAttributeValue($object)
    {
        return $object->getData($this->getAttribute()->getAttributeCode());
    }
}

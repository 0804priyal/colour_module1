<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Model\Entity\Attribute\Backend;

abstract class AbstractUnit extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \HS\BasePrice\Helper\Conversion
     */
    protected $conversionHelper;

    /**
     * @param \HS\BasePrice\Helper\Conversion $conversionHelper
     */
    public function __construct(
        \HS\BasePrice\Helper\Conversion $conversionHelper
    ) {
        $this->conversionHelper = $conversionHelper;
    }

    /**
     * Validate.
     *
     * @param \Magento\Catalog\Model\Product $object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return bool
     */
    public function validate($object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if (empty($value)) {
            return parent::validate($object);
        }

        $from = $this->getProductUnitAttributeValue($object);
        $to = $this->getReferenceUnitAttributeValue($object);
        if (false === $this->conversionHelper->getConversionRate($from, $to)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('There is no rate specified for conversion from %1 to %2.', $value, $to)
            );
        }

        return true;
    }

    /**
     * Retrieve product unit.
     *
     * @return string
     */
    abstract protected function getProductUnitAttributeValue($object);

    /**
     * Retrieve reference unit.
     *
     * @return string
     */
    abstract protected function getReferenceUnitAttributeValue($object);
}

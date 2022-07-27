<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Model\Entity\Attribute\Backend;

class Amount extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     */
    public function __construct(
        \Magento\Framework\Locale\FormatInterface $localeFormat
    ) {
        $this->localeFormat = $localeFormat;
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

        if (!$this->isPositive($value)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please enter a number greater than 0 in this field.')
            );
        }

        return true;
    }

    /**
     * Returns whether the value is greater than, or equal to, zero.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isPositive($value)
    {
        $value = $this->localeFormat->getNumber($value);
        $isNegativeOrZero = $value <= 0;

        return !$isNegativeOrZero;
    }
}

<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Model;

class BasePrice extends \Magento\Framework\Model\AbstractModel
{
    const UNIT = 'UNIT_';
    const UNIT_SHORT = 'UNIT_SHORT_';

    /**
     * @var \HS\BasePrice\Helper\Conversion
     */
    protected $conversionHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @param \Magento\Framework\Pricing\Helper\Data $helper
     * @param \HS\BasePrice\Helper\Conversion        $conversionHelper
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \HS\BasePrice\Helper\Conversion $conversionHelper
    ) {
        $this->conversionHelper = $conversionHelper;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * Retrieve all options.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $units = $this->conversionHelper->getAvaiableMetricUnits();
        $optionArray = [];
        foreach ($units as $code => $unit) {
            $optionArray[] = ['label' => __($unit), 'value' => $code];
        }

        return $optionArray;
    }

    /**
     * Undocumented function
     *
     * @param [type] $product
     * @return void
     */
    public function getBasePriceProduct($product)
    {
        $finalPrice = $product->getPriceInfo()->getPrice('final_price');
        if (method_exists($finalPrice, 'getMinProduct') && $finalPrice->getMinProduct()) {
            $product = $finalPrice->getMinProduct();
        }

        return $product;
    }

    /**
     * Retrieve base price of product.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return void|float
     */
    public function getBasePrice($product)
    {
        $productAmount = $this->getBasePriceAmount($product);
        if ($productAmount <= 0) {
            return false;
        }

        // Below line works using the price entered as is with/without tax but doesn't work when price entered is without tax but is displayed with tax and vice versa.
        // $productPrice = $product->getPriceInfo()->getPrice('final_price' /*priceCode from plugin aroundRender*/)->getValue();

        // Below line works with the value that is displayed in the frontend. Switch with above line if needed.
        $finalPrice = $product->getPriceInfo()->getPrice('final_price');
        $productPrice = $finalPrice->getAmount()->getValue();
        $productUnit = $this->getBasePriceUnit($product);
        $rate = $this->conversionHelper->getConversionRate(
            $productUnit,
            $this->getBasePriceReferenceUnit($product)
        );
        if (false === $rate) {
            return false;
        }
        $result = $productPrice / $productAmount / $rate * $this->getBasePriceReferenceAmount($product);
        return $result;
    }

    /**
     * Retrieve currency formatted base price.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getFormattedBasePrice($product)
    {
        $basePrice = $this->getBasePrice($product);
        if ($basePrice) {
            return $this->pricingHelper->currency($basePrice, true, false);
        }

        return false;
    }

    /**
     * Retrive base price amount value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return float
     */
    public function getBasePriceAmount($product)
    {
        return (float) $this->getProductAttributeValue($product, 'base_price_amount');
    }

    /**
     * Retrive base price unit long value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceUnit($product)
    {
        return $this->getProductAttributeValue($product, 'base_price_unit');
    }

    /**
     * Retrive base price unit long value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceUnitLong($product)
    {
        return __(self::UNIT.$this->getBasePriceUnit($product));
    }

    /**
     * Retrive base price unit short value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceUnitShort($product)
    {
        return __(self::UNIT_SHORT.$this->getBasePriceUnit($product));
    }

    /**
     * Retrive base price reference amount value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return float
     */
    public function getBasePriceReferenceAmount($product)
    {
        return (float) $this->getProductAttributeValue($product, 'base_price_reference_amount');
    }

    /**
     * Retrive base price unit value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceReferenceUnit($product)
    {
        return $this->getProductAttributeValue($product, 'base_price_reference_unit');
    }

    /**
     * Retrive base price unit long value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceReferenceUnitLong($product)
    {
        return __(self::UNIT.$this->getBasePriceReferenceUnit($product));
    }

    /**
     * Retrive base price amount short value.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceReferenceUnitShort($product)
    {
        return __(self::UNIT_SHORT.$this->getBasePriceReferenceUnit($product));
    }

    /**
     * Retrive product attribute value.
     *
     * @param Magento\Catalog\Model\Product $product
     * @param string                        $attributeCode
     *
     * @return string
     */
    public function getProductAttributeValue($product, $attributeCode)
    {
        return $product->getResource()->getAttributeRawValue(
            $product->getId(),
            $attributeCode,
            $product->getStoreId()
        );
    }
}

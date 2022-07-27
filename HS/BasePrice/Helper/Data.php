<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_ENABLED = 'hs_base_price/general/enabled';
    const CONFIG_PRODUCT_PAGE_LABEL = 'hs_base_price/general/product_page_label';
    const CONFIG_LIST_PAGE_LABEL = 'hs_base_price/general/list_page_label';
    const CONFIG_UNITS = 'hs_base_price/general/units';
    const CONFIG_SHOW_ON_PRODUCT_VIEW_PAGE = 'hs_base_price/general/show_on_product_view_page';
    const CONFIG_SHOW_ON_PRODUCT_LIST_PAGE = 'hs_base_price/general/show_on_product_list_page';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Currently selected store ID if applicable.
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * @var \HS\BasePrice\Model\BasePrice
     */
    protected $basePrice;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * List of format variables that can be used in the display text.
     *
     * @var array
     */
    protected $formatVars = [
        '{{base_price}}' => 'getFormattedBasePrice',
        '{{product_amount}}' => 'getBasePriceAmount',
        '{{product_amount_unit}}' => 'getBasePriceUnitLong',
        '{{product_amount_unit_short}' => 'getBasePriceUnitShort',
        '{{reference_amount}}' => 'getBasePriceReferenceAmount',
        '{{reference_amount_unit}}' => 'getBasePriceReferenceUnitLong',
        '{{reference_amount_unit_short}}' => 'getBasePriceReferenceUnitShort',
    ];

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param \Magento\Framework\Registry            $coreRegistry
     * @param \HS\BasePrice\Model\BasePrice          $basePrice
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper,
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \HS\BasePrice\Model\BasePrice $basePrice,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ) {
        $this->registry = $coreRegistry;
        $this->basePrice = $basePrice;
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context);
    }

    /**
     * Set a specified store ID value.
     *
     * @param int $store
     *
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;

        return $this;
    }

    /**
     * Return true if active and false otherwise.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * Retrieve units from config.xml.
     *
     * @return string
     */
    public function getUnits()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_UNITS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Product page label format when base price is specified.
     *
     * @return string
     */
    public function getProductPageLabelFormat($store = null)
    {
        return  $this->scopeConfig->getValue(
            self::CONFIG_PRODUCT_PAGE_LABEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * List page label format when base price is specified.
     *
     * @return string
     */
    public function getListPageLabelFormat($store = null)
    {
        return  $this->scopeConfig->getValue(
            self::CONFIG_LIST_PAGE_LABEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve base price formatted label.
     *
     * @param string                        $format
     * @param Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getBasePriceLabel($format, $product, $html = true)
    {
        $product = $this->basePrice->getBasePriceProduct($product);
        foreach ($this->formatVars as $var => $function) {
            $varValue = $this->basePrice->$function($product);
            if (!$varValue) {
                $format = '';
                break;
            }

            $format = str_replace($var, $varValue, $format);
        }

        if (true === $html) {
            return sprintf(
                '
                    <div class="base-price-text">%s</div>
                    <style type="text/css">.base-price-text:empty {display: none}</style>
                ',
                $format
            );
        }

        return $format;
    }

    /**
     * Checks whether or not to show base price.
     *
     * @return bool
     */
    public function canShowBasePrice($product)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        // if (!$this->hasBasePriceAmount($product)) {
        //     return false;
        // }

        return true;
    }

    /**
     * Checks whether show on product page.
     *
     * @return bool
     */
    public function canShowOnProductViewPage()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_SHOW_ON_PRODUCT_VIEW_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * Checks whether show on list page.
     *
     * @return bool
     */
    public function canShowOnProductListPage()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_SHOW_ON_PRODUCT_LIST_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }

    /**
     * Checks whether or not the product has base price amount.
     *
     * @param Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function hasBasePriceAmount($product)
    {
        $basePriceAmount = $this->getProductAttributeValue($product, 'base_price_amount');
        if ($basePriceAmount) {
            return true;
        }

        return false;
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

    /**
     * Format price currency.
     *
     * @param            $value
     * @param bool|true  $format
     * @param bool|false $includeContainer
     *
     * @return mixed
     */
    public function currency($value, $format = true, $includeContainer = false)
    {
        return $this->pricingHelper->currency($value, $format, $includeContainer);
    }
}

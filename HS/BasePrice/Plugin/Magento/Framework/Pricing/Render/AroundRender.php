<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Plugin\Magento\Framework\Pricing\Render;

use Magento\Framework\Pricing\SaleableInterface;

class AroundRender
{
    /**
     * @var \HS\BasePrice\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Constructor.
     *
     * @param \HS\BasePrice\Helper\Data   $helper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \HS\BasePrice\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->helper = $helper;
        $this->registry = $coreRegistry;
    }

    /**
     * Set message to display when cannot show price.
     *
     * @param \Magento\Framework\Pricing\Render $priceBlock
     * @param string                            $result
     *
     * @return string
     */
    public function aroundRender(
        \Magento\Framework\Pricing\Render $priceBlock,
        $isSalable,
        $priceCode,
        SaleableInterface $saleableItem,
        array $arguments = []
    ) {
        if ((false === $this->helper->canShowBasePrice($saleableItem)) || $saleableItem->getIsSetHidePrice()) {
            return $isSalable($priceCode, $saleableItem, $arguments);
        }

        $result = $isSalable($priceCode, $saleableItem, $arguments);
        if (isset($arguments['zone']) && $arguments['zone'] == 'item_list') {
            $labelFormat = $this->helper->getListPageLabelFormat($saleableItem->getStoreId());
        } else {
            $labelFormat = $this->helper->getProductPageLabelFormat($saleableItem->getStoreId());
        }

        $textToDisplay = sprintf(
            '<!--hs_hpp-blank-%d-->%s<!--/hs_hpp-->',
            $saleableItem->getIsDisableHidePricePerProduct(),
            $this->helper->getBasePriceLabel($labelFormat, $saleableItem)
        );

        $saleableItem->setIsSetHidePrice(true);

        return $result.$textToDisplay;
    }
}

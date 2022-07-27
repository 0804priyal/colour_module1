<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Rewrite\Magento\ConfigurableProduct\Pricing\Price;

use Magento\Framework\App\ObjectManager;
use Magento\ConfigurableProduct\Pricing\Price\PriceResolverInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;

class FinalPrice extends \Magento\ConfigurableProduct\Pricing\Price\FinalPrice
{
    /**
     * @var LowestPriceOptionsProviderInterface
     */
    private $lowestPriceOptionsProvider;

    private $minProduct = null;

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     * @param float $quantity
     * @param \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param PriceResolverInterface $priceResolver
     */
    public function __construct(
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        $quantity,
        \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceResolverInterface $priceResolver,
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider = null
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency, $priceResolver);
        $this->priceResolver = $priceResolver;
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider ?:
            ObjectManager::getInstance()->get(LowestPriceOptionsProviderInterface::class);
    }

    /**
     * Returns product with minimal price
     *
     * @return Product
     */
    public function getMinProduct()
    {
        if (null === $this->minProduct) {
            $price = null;    
            foreach ($this->lowestPriceOptionsProvider->getProducts($this->product) as $subProduct) {
                $productPrice = $this->priceResolver->resolvePrice($subProduct);                
                if (!isset($price) || $productPrice < $price) {
                    $this->minProduct = $subProduct;
                    $price = $productPrice;
                }
            }    
        }
        return $this->minProduct;
    }
}
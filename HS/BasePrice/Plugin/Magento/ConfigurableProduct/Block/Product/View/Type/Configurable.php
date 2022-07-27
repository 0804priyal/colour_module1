<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Configurable
{
    /**
     * @var \HS\BasePrice\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param PriceCurrencyInterface                   $priceCurrency
     */
    public function __construct(
        \HS\BasePrice\Helper\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->helper = $helper;
        $this->priceCurrency = $priceCurrency;
        $this->jsonEncoder = $jsonEncoder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * afterGetJsonConfig plugin.
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurable
     * @param  $result
     *
     * @return string
     */
    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurable,
        $result
    ) {
        $config = json_decode($result, true);
        if ($this->helper->isEnabled()) {
            $config['basePriceTexts'] = $this->getAllOptionBasePriceTexts($config['optionPrices']);
        }
        $config['isEnabledBasePrice'] = $this->helper->isEnabled();

        return $this->jsonEncoder->encode($config);
    }

    /**
     * @return array
     */
    private function getAllOptionBasePriceTexts($optionPrices)
    {
        if (empty($optionPrices)) {
            return [];
        }

        $products = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => array_keys($optionPrices)]);

        $texts = [];
        $labelFormat = $this->helper->getProductPageLabelFormat();
        foreach ($products as $product) {
            $product->setFinalPrice($optionPrices[$product->getId()]['finalPrice']['amount']);
            $texts[$product->getId()] = $this->helper->getBasePriceLabel($labelFormat, $product, false);
        }

        return $texts;
    }
}

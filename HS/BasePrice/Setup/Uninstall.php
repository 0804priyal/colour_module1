<?php

namespace HS\BasePrice\Setup;

use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * EAV setup factory.
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Module uninstall code.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributes = ['base_price_amount', 'base_price_unit', 'base_price_reference_amount', 'base_price_reference_unit'];
        foreach ($attributes as $attributeCode) {
            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeCode
            );
        }

        $setup->endSetup();
    }
}

<?php
/**
 * @category  HS
 *
 * @copyright Copyright (c) 2015 Hungersoft (http://www.hungersoft.com)
 * @license   http://www.hungersoft.com/license.txt Hungersoft General License
 */

namespace HS\BasePrice\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
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
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'base_price_amount',
            [
                'type' => 'varchar',
                'label' => 'Amount in product',
                'input' => 'text',
                'backend' => 'HS\BasePrice\Model\Entity\Attribute\Backend\Amount',
                'sort_order' => 1,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Base Price',
                'apply_to' => 'simple,bundle,configurable',
                'required' => false,
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'base_price_unit',
            [
                'type' => 'varchar',
                'label' => 'Amount unit',
                'input' => 'select',
                'source' => 'HS\BasePrice\Model\Entity\Attribute\Source\Unit',
                'backend' => 'HS\BasePrice\Model\Entity\Attribute\Backend\Product\Unit',
                'sort_order' => 2,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Base Price',
                'required' => false,
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'base_price_reference_amount',
            [
                'type' => 'varchar',
                'label' => 'Reference Amount',
                'input' => 'text',
                'backend' => 'HS\BasePrice\Model\Entity\Attribute\Backend\Amount',
                'sort_order' => 3,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Base Price',
                'apply_to' => 'simple,bundle,configurable',
                'default' => '1',
                'required' => false,
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'base_price_reference_unit',
            [
                'type' => 'varchar',
                'label' => 'Reference Unit',
                'input' => 'select',
                'source' => 'HS\BasePrice\Model\Entity\Attribute\Source\Unit',
                'backend' => 'HS\BasePrice\Model\Entity\Attribute\Backend\Reference\Unit',
                'sort_order' => 4,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Base Price',
                'required' => false,
            ]
        );
    }
}

<?php

namespace TrainingNikhil\Dailydeal\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Install attributes
 */
class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * Init
     *
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }
     public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dailydeal_enable',
                [
                    'group' => 'Dailydeal',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Dailydeal enable',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '1',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
                ]
            );

        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_dailydeal_end_date',
            [
                'type' => 'datetime',
                'group' => 'Dailydeal',
                'backend' => '',
                'frontend' => 'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime',
                'label' => 'Product Dailydeal End Date',
                'input' => 'date',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'sort_order' => 9,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
            ]
        ); 

        $setup->endSetup();
    }
}
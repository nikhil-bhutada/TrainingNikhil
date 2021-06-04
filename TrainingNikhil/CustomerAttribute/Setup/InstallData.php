<?php
namespace TrainingNikhil\CustomerAttribute\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    private $eavSetupFactory;
    
    private $eavConfig;
    
    private $attributeResource;
    
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->removeAttribute(Customer::ENTITY, "fathername");
        $eavSetup->removeAttribute(Customer::ENTITY, "mothername");

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $eavSetup->addAttribute(Customer::ENTITY, 'fathername', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Father Name',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 990,
            'position' => 990,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_html_allowed_on_front' => false,
            'visible_on_front' =>true
        ]);
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'fathername');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

		$attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);

        $this->attributeResource->save($attribute);


        
        

        $eavSetup->addAttribute(Customer::ENTITY, 'mothername', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Mother Name',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 990,
            'position' => 990,
            'system' => 0,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_html_allowed_on_front' => false,
            'visible_on_front' =>true
        ]);
        
        $motherattribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'mothername');
        $motherattribute->setData('attribute_set_id', $attributeSetId);
        $motherattribute->setData('attribute_group_id', $attributeGroupId);

		$motherattribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);

        $this->attributeResource->save($motherattribute);
    }
}
?>


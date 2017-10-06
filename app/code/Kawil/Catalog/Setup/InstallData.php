<?php
// @codingStandardsIgnoreFile
namespace Kawil\Catalog\Setup;

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
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'kawil_lieferant',
            [
                'type' => 'int',
                'label' => 'Lieferant',
                'input' => 'select',
                'visible' => true,
                'user_defined' => true,
                'source' => '',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'used_in_product_listing' => true,
                'apply_to' => '',
                'group' => 'General',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option'     => array (
                    'values' => array(
                        0 => 'Small',
                        1 => 'Medium',
                        2 => 'Large',
                    )
                ),
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'kawil_alternative_lieferantent',
            [
                'type' => 'varchar',
                'label' => 'Alternative Lieferanten',
                'input' => 'multiselect',
                'visible' => true,
                'user_defined' => true,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'required' => false,
                'used_in_product_listing' => true,
                'apply_to' => '',
                'group' => 'General',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option'     => array (
                    'values' => array(
                        0 => 'Small',
                        1 => 'Medium',
                        2 => 'Large',
                    )
                ),
            ]
        );

        $setup->endSetup();
    }
}

<?php
namespace Aheadworks\FaqFree\Setup\Patch\Data;

use Aheadworks\FaqFree\Api\Data\ProductAttributeInterface;
use Aheadworks\FaqFree\Model\Source\Product\Attribute\Articles;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;

class InstallProductArticlesAttribute implements DataPatchInterface, PatchRevertableInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Create product articles attribute
     */
    public function apply()
    {
        $groupName = 'FAQ Articles';

        /** @var EavSetup $eavSetup */
        $eavSetup =  $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            ProductAttributeInterface::CODE_AW_FAQ_ARTICLES,
            [
                'group' => $groupName,
                'type'  => 'text',
                'input' => 'multiselect',
                'backend' => '',
                'frontend' => '',
                'label' => 'Faq Articles',
                'class' => '',
                'source' => Articles::class,
                'global' =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }

    /**
     * Remove all FAQ eav attributes
     */
    public function revert()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, ProductAttributeInterface::CODE_AW_FAQ_ARTICLES);
    }

    /**
     * Get patch aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get version
     *
     * @return string
     */
    public static function getVersion()
    {
        return '1.4.0';
    }
}

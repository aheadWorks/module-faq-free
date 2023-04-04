<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Aheadworks\FaqFree\Model\Source\Product\Attribute\Articles\Proxy as ArticlesProxy;
use Aheadworks\FaqFree\Api\Data\ProductAttributeInterface;

class ChangeSourceForFaqArticlesAttribute implements
    DataPatchInterface,
    PatchRevertableInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    /**
     * Update 'aw_faq_articles' product attribute
     *
     * @return self
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->updateAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_AW_FAQ_ARTICLES,
            'source_model',
            ArticlesProxy::class
        );

        return $this;
    }

    /**
     * Revert patch
     *
     * @return bool
     */
    public function revert()
    {
        return true;
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
        return [
            InstallProductArticlesAttribute::class
        ];
    }
}

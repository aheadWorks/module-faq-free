<?php
namespace Aheadworks\FaqFree\Plugin\Controller\Catalog\Adminhtml\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Model\Product;
use Aheadworks\FaqFree\Api\Data\ProductAttributeInterface;

class InitializationHelperPlugin
{
    /**
     * Add faq articles extension attributes after initialize product
     *
     * @param InitializationHelper $subject
     * @param \Closure $proceed
     * @param Product $product
     * @param array $productData
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInitializeFromData(
        InitializationHelper $subject,
        \Closure $proceed,
        Product $product,
        array $productData
    ) {
        $product = $proceed($product, $productData);
        $extension = $product->getExtensionAttributes();
        $extension->setAwFaqArticles($this->prepareProductArticlesIds($product));
        $product->setExtensionAttributes($extension);

        return $product;
    }

    /**
     * Prepare product articles ids
     *
     * @param array $productData
     * @return int[]
     */
    private function prepareProductArticlesIds($productData)
    {
        $articlesIds = [];

        if (!empty($productData[ProductAttributeInterface::CODE_AW_FAQ_ARTICLES])
            && is_array($productData[ProductAttributeInterface::CODE_AW_FAQ_ARTICLES])
        ) {
            foreach ($productData[ProductAttributeInterface::CODE_AW_FAQ_ARTICLES] as $article) {
                if (!empty($article['id'])) {
                    $articlesIds[] = $article['id'];
                }
            }
        }

        return $articlesIds;
    }
}

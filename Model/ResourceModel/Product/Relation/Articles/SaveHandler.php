<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Product\Relation\Articles;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\FaqFree\Model\ProductArticleRepository;
use Magento\Catalog\Api\Data\ProductInterface;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var ProductArticleRepository
     */
    private $productArticleRepository;

    /**
     * SaveHandler constructor.
     * @param ProductArticleRepository $productArticleRepository
     */
    public function __construct(
        ProductArticleRepository $productArticleRepository
    ) {
        $this->productArticleRepository = $productArticleRepository;
    }

    /**
     * Attach related articles
     *
     * @param ProductInterface $entity
     * @param array $arguments
     * @return ProductInterface
     */
    public function execute($entity, $arguments = [])
    {
        $faqArticlesIds = !empty($entity->getExtensionAttributes()->getAwFaqArticles())
            ? $entity->getExtensionAttributes()->getAwFaqArticles()
            : [];
        $this->productArticleRepository->delete($entity);
        $this->productArticleRepository->save($entity, $faqArticlesIds);

        return $entity;
    }
}

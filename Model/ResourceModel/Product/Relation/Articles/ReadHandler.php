<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Product\Relation\Articles;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\FaqFree\Model\ProductArticleRepository;
use Magento\Catalog\Api\Data\ProductInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var ProductArticleRepository
     */
    private $productArticleRepository;

    /**
     * ReadHandler constructor.
     * @param ProductArticleRepository $productArticleRepository
     */
    public function __construct(
        ProductArticleRepository $productArticleRepository
    ) {
        $this->productArticleRepository = $productArticleRepository;
    }

    /**
     * Attach extension attributes
     *
     * @param ProductInterface $entity
     * @param array $arguments
     * @return ProductInterface
     */
    public function execute($entity, $arguments = [])
    {
        $faqArticles = $this->productArticleRepository->get($entity);
        $extension = $entity->getExtensionAttributes();
        $extension->setAwFaqArticles($faqArticles);
        $entity->setExtensionAttributes($extension);

        return $entity;
    }
}

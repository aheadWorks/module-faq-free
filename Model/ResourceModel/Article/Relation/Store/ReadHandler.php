<?php

namespace Aheadworks\FaqFree\Model\ResourceModel\Article\Relation\Store;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Set lookup store ids
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getArticleId()) {
            $entity->setData('store_ids', $this->lookupStoreIds((int)$entity->getArticleId()));
        }

        return $entity;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupStoreIds($articleId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(ArticleInterface::class);

        $connection = $this->resourceConnection->getConnectionByName(
            $entityMetadata->getEntityConnectionName()
        );

        $select = $connection->select()
            ->from(['fas' => $this->resourceConnection->getTableName('aw_faq_article_store')], 'store_ids')
            ->where('fas.' . $entityMetadata->getIdentifierField() . ' = :articleId');

        return $connection->fetchCol($select, ['articleId' => (int)$articleId]);
    }
}

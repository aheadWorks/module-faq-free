<?php

namespace Aheadworks\FaqFree\Model\ResourceModel\Category\Relation\Store;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;

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
        if ($entity->getCategoryId()) {
            $entity->setData('store_ids', $this->lookupStoreIds((int)$entity->getCategoryId()));
        }

        return $entity;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $categoryId
     * @return array
     */
    public function lookupStoreIds($categoryId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(CategoryInterface::class);

        $connection = $this->resourceConnection->getConnectionByName(
            $entityMetadata->getEntityConnectionName()
        );

        $select = $connection->select()
            ->from(['fas' => $this->resourceConnection->getTableName('aw_faq_category_store')], 'store_ids')
            ->where('fas.' . $entityMetadata->getIdentifierField() . ' = :categoryId');

        return $connection->fetchCol($select, ['categoryId' => (int)$categoryId]);
    }
}

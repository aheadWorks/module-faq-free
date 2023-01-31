<?php

namespace Aheadworks\FaqFree\Model\ResourceModel\Category\Relation\Store;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;

class SaveHandler implements ExtensionInterface
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
     * Insert data
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(CategoryInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CategoryInterface::class)->getEntityConnectionName()
        );

        $oldStores = $this->lookupStoreIds((int)$entity->getCategoryId());
        $newStores = (array)$entity->getStoreIds();
        $table = $this->resourceConnection->getTableName('aw_faq_category_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_ids IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeIds) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_ids' => (int)$storeIds
                ];
            }

            $connection->insertMultiple($table, $data);
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
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CategoryInterface::class)->getEntityConnectionName()
        );

        $entityMetadata = $this->metadataPool->getMetadata(CategoryInterface::class);

        $select = $connection->select()
            ->from(['fas' => $this->resourceConnection->getTableName('aw_faq_category_store')], 'store_ids')
            ->where('fas.' . $entityMetadata->getIdentifierField() . ' = :categoryId');

        return $connection->fetchCol($select, ['categoryId' => (int)$categoryId]);
    }
}

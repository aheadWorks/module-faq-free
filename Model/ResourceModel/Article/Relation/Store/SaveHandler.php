<?php

namespace Aheadworks\FaqFree\Model\ResourceModel\Article\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\EntityManager\MetadataPool;
use \Magento\Framework\App\ResourceConnection;

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
        $entityMetadata = $this->metadataPool->getMetadata(ArticleInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $this->resourceConnection->getConnectionByName(
            $entityMetadata->getEntityConnectionName()
        );

        $oldStores = $this->lookupStoreIds((int)$entity->getArticleId());
        $newStores = (array)$entity->getStoreIds();
        $table = $this->resourceConnection->getTableName('aw_faq_article_store');

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

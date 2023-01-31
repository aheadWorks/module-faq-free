<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Article\Relation\Tag;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\TagInterface;
use Aheadworks\FaqFree\Api\Data\TagInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Tag as ResourceTag;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var TagInterfaceFactory
     */
    private $tagFactory;

    /**
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param TagInterfaceFactory $tagFactory
     */
    public function __construct(
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        TagInterfaceFactory $tagFactory
    ) {
        $this->entityManager = $entityManager;
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tagFactory = $tagFactory;
    }

    /**
     * Attach related tags
     *
     * @param ArticleInterface $entity
     * @param array $arguments
     * @return ArticleInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $tagNames = $entity->getTagNames() ? : [];

        $entityTags = [];
        $existingTags = [];
        foreach ($this->getTagsData($tagNames, $entityId) as $data) {
            $tagName = $data[TagInterface::NAME];
            if ($data[ArticleInterface::ARTICLE_ID] == $entityId) {
                $entityTags[$data[TagInterface::ID]] = $tagName;
            } else {
                $existingTags[$data[TagInterface::ID]] = $tagName;
            }
        }

        $new = array_udiff($tagNames, $entityTags, 'strcasecmp');
        $toCreate = array_udiff($new, $existingTags, 'strcasecmp');
        $toInsert = array_udiff($new, $toCreate, 'strcasecmp');
        $toDelete = array_udiff($entityTags, $tagNames, 'strcasecmp');

        if ($toInsert) {
            $this->saveRelations(
                $entityId,
                array_keys(array_uintersect($existingTags, $toInsert, 'strcasecmp'))
            );
        }
        if ($toCreate) {
            $this->saveRelations($entityId, $this->createTags($toCreate));
        }
        if ($toDelete) {
            $this->getConnection()->delete(
                $this->resourceConnection->getTableName(ResourceArticle::FAQ_ARTICLE_TAG_TABLE_NAME),
                [
                    'article_id = ?' => $entityId,
                    'tag_id IN (?)' => array_keys(array_uintersect($entityTags, $toDelete, 'strcasecmp'))
                ]
            );
        }

        return $entity;
    }

    /**
     * Get tags data with given tag names or associated to a given entity Id
     *
     * @param array $tagNames
     * @param int $entityId
     * @return array
     */
    public function getTagsData(array $tagNames, $entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['tag' => $this->resourceConnection->getTableName(ResourceTag::MAIN_TABLE_NAME)],
                [TagInterface::ID, TagInterface::NAME]
            )->joinLeft(
                ['tag_article' => $this->resourceConnection->getTableName(ResourceArticle::FAQ_ARTICLE_TAG_TABLE_NAME)],
                'tag.id = tag_article.tag_id',
                [ArticleInterface::ARTICLE_ID]
            )
            ->where('name IN(?)', $tagNames)
            ->orWhere('article_id = ?', $entityId);
        return $connection->fetchAll($select);
    }

    /**
     * Create tags, return IDs of created tags
     *
     * @param array $tagNames
     * @return int[]
     */
    private function createTags(array $tagNames)
    {
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            /** @var TagInterface $tag */
            $tag = $this->tagFactory->create();
            $tag->setName($tagName);
            $this->entityManager->save($tag);
            $tagIds[] = $tag->getId();
        }
        return $tagIds;
    }

    /**
     * Insert rows with tag IDs into tag relation table
     *
     * @param int $entityId
     * @param array $tagIds
     * @return void
     */
    private function saveRelations($entityId, array $tagIds)
    {
        $data = [];
        foreach ($tagIds as $tagId) {
            $data[] = [
                'tag_id' => $tagId,
                ArticleInterface::ARTICLE_ID => $entityId
            ];
        }
        $this->getConnection()->insertMultiple(
            $this->resourceConnection->getTableName(Resourcearticle::FAQ_ARTICLE_TAG_TABLE_NAME),
            $data
        );
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(ArticleInterface::class)->getEntityConnectionName()
        );
    }
}

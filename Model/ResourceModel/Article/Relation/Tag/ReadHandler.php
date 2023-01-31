<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Article\Relation\Tag;

use Aheadworks\FaqFree\Api\Data\TagInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Tag as ResourceTag;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Attach related tags
     *
     * @param ArticleInterface $entity
     * @param array $arguments
     * @return ArticleInterface
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(ArticleInterface::class)->getEntityConnectionName()
            );

            $tagTableName = $this->resourceConnection->getTableName(ResourceTag::MAIN_TABLE_NAME);
            $tagArticleTableName = $this->resourceConnection->getTableName(ResourceArticle::FAQ_ARTICLE_TAG_TABLE_NAME);

            $select = $connection->select()
                ->from(['tag' => $tagTableName], TagInterface::NAME)
                ->joinLeft(
                    ['tag_article' => $tagArticleTableName],
                    'tag.id = tag_article.tag_id',
                    []
                )->where('tag_article.article_id = :id', $entityId);
            $tagNames = $connection->fetchCol($select, [TagInterface::ID => $entityId]);
            $entity->setTagNames($tagNames);
        }
        return $entity;
    }
}

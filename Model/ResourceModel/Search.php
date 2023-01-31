<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Model\ResourceModel;

use Aheadworks\FaqFree\Api\Data\TagInterface;
use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Collection;
use Aheadworks\FaqFree\Model\ResourceModel\Article\CollectionFactory as ArticleCollectionFactory;

class Search
{
    /**
     * @var Fulltext
     */
    private $fullText;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Fulltext $fulltext
     * @param ArticleCollectionFactory $articleCollectionFactory
     */
    public function __construct(
        Fulltext $fulltext,
        ArticleCollectionFactory $articleCollectionFactory
    ) {
        $this->fullText = $fulltext;
        $this->collection = $articleCollectionFactory->create();
    }

    /**
     * Search Articles with query string
     *
     * @param string $searchString
     * @param int|null $limit
     * @param bool $findByTag
     * @param int $storeId
     * @return array
     */
    public function searchQuery(string $searchString, ?int $limit, bool $findByTag, int $storeId): array
    {
        $this->prepareCollection($storeId);
        if (!$findByTag) {
            $this->attachArticleCondition($searchString, $limit);
        }
        $this->attachTagCondition($searchString, $findByTag);

        return $this->collection->getItems();
    }

    /**
     * Perform all necessary operations to prepare collection for loading
     *
     * @param int $storeId
     */
    private function prepareCollection(int $storeId)
    {
        $this->collection->addStoreFilter($storeId);
        $this->collection->getSelect()
            ->where('main_table.' . ArticleInterface::IS_ENABLE, true)
            ->group('main_table.' . ArticleInterface::ARTICLE_ID);
    }

    /**
     * Attach article condition to collection
     *
     * @param string $searchString
     * @param int|null $limit
     */
    private function attachArticleCondition(string $searchString, ?int $limit)
    {
        $matchQuery = $this->getMatchExpression($searchString, [ArticleInterface::TITLE, ArticleInterface::CONTENT]);

        $this->collection->getSelect()
            ->columns(['articleScore' => $matchQuery])
            ->order('articleScore DESC')
            ->orHaving('articleScore > 0')
            ->limit($limit);
    }

    /**
     * Attach tag condition to collection
     *
     * @param string $searchString
     * @param bool $findByTag
     */
    private function attachTagCondition(string $searchString, bool $findByTag)
    {
        $select = $this->collection->getSelect();

        $select->joinLeft(
            ['article_tag_table' => $this->collection->getTable(Article::FAQ_ARTICLE_TAG_TABLE_NAME)],
            'article_tag_table.article_id = main_table.article_id',
            []
        )->joinLeft(
            ['tag_table' => $this->collection->getTable(Tag::MAIN_TABLE_NAME)],
            'article_tag_table.tag_id = tag_table.id',
            []
        );

        if ($findByTag) {
            $select->where('name = ?', $searchString);
        } else {
            $matchQuery = $this->getMatchExpression($searchString, [TagInterface::NAME]);
            $select
                ->columns(['tagScore' => $matchQuery])
                ->order('tagScore DESC')
                ->orHaving('tagScore > 0');
        }
    }

    /**
     * Return Search Expression
     *
     * @param string $searchString
     * @param array $fields
     * @return \Zend_Db_Expr
     */
    private function getMatchExpression(string $searchString, array $fields): \Zend_Db_Expr
    {
        return new \Zend_Db_Expr(
            $this->fullText->getMatchQuery(
                $fields,
                $searchString,
                Fulltext::FULLTEXT_MODE_BOOLEAN
            )
        );
    }
}

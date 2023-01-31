<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Article;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\TagInterface;
use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\ProductArticleRepository;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ArticleResource;
use Aheadworks\FaqFree\Model\ResourceModel\Tag as ResourceTag;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as MagentoAbstractCollection;
use Magento\Store\Model\Store;
use Aheadworks\FaqFree\Model\ResourceModel\Category as ResourceCategory;

/**
 * FAQ Article Collection
 */
class Collection extends MagentoAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'article_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Article::class, ArticleResource::class);
        $this->_map['fields'][Article::ARTICLE_ID] = 'main_table.article_id';
        $this->_map['fields'][Article::IS_ENABLE] = 'main_table.is_enable';
        $this->_map['fields']['store'] = 'store_table.store_ids';
        $this->_map['fields'][ArticleInterface::PRODUCT_IDS] = 'product_article_table.product_id';
        $this->_map['fields'][ArticleInterface::IS_CATEGORY_ENABLE] = 'category_table.is_enable';
    }

    /**
     * Returns pairs url_key|title for unique identifiers and pairs url_key|article_id - title for non-unique
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $result = [];
        $existingIdentifiers = [];
        foreach ($this as $item) {
            $identifier = $item->getData(Article::URL_KEY);

            $data['value'] = $identifier;
            $data['label'] = $item->getData(Article::TITLE);

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData(Article::ARTICLE_ID);
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * Attach additional data to article
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this
            ->attachStore()
            ->attachTagData();
        return parent::_afterLoad();
    }

    /**
     * Attach store to collection items
     *
     * @return $this
     */
    protected function attachStore()
    {
        $linkField = Article::ARTICLE_ID;

        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['faq' => $this->getTable('aw_faq_article_store')])
                ->where('faq.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $resultData = [];
                foreach ($result as $resultItem) {
                    $resultData[$resultItem[$linkField]][] = $resultItem[Article::STORE_IDS];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($resultData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $resultData[$linkedId]);
                    $item->setData(Article::STORE_IDS, $resultData[$linkedId]);
                }
            }
        }
        return $this;
    }

    /**
     * Attach tag ids and names to collection items
     *
     * @return $this
     */
    protected function attachTagData()
    {
        $articleIds = $this->getColumnValues(Article::ARTICLE_ID);
        if (count($articleIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['tags_table' => $this->getTable(ResourceTag::MAIN_TABLE_NAME)])
                ->joinLeft(
                    ['tag_article_linkage_table' => $this->getTable(ArticleResource::FAQ_ARTICLE_TAG_TABLE_NAME)],
                    'tags_table.id = tag_article_linkage_table.tag_id',
                    [Article::ARTICLE_ID => 'tag_article_linkage_table.article_id']
                )
                ->where('tag_article_linkage_table.article_id IN (?)', $articleIds);
            /** @var \Magento\Framework\DataObject $item */
            $result = $connection->fetchAll($select);
            foreach ($this as $item) {
                $tagIds = [];
                $tagNames = [];
                $articleId = $item->getData(Article::ARTICLE_ID);
                foreach ($result as $data) {
                    if ($data[Article::ARTICLE_ID] == $articleId) {
                        $tagIds[] = $data[TagInterface::ID];
                        $tagNames[] = $data[TagInterface::NAME];
                    }
                }
                $item->setData(Article::TAG_IDS, $tagIds);
                $item->setData(Article::TAG_NAMES, $tagNames);
            }
        }
        return $this;
    }

    /**
     * Attach additional data
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable();
        $this->joinProductArticleRelationTable();
        $this->joinCategoryRelationTable();
        parent::_renderFiltersBefore();
    }

    /**
     * Add store field to filter
     *
     * @param string $field
     * @param mixed $condition
     *
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === Article::STORE_IDS) {
            return $this->addStoreFilter($condition, false);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add store filter to collection
     *
     * @param int|array|Store $store
     * @param bool $withDefaultStore
     * @return $this
     */
    public function addStoreFilter($store, $withDefaultStore = true)
    {
        $storeToFilter = [];
        if (!is_array($store)) {
            if ($store instanceof Store) {
                $storeToFilter[] = $store->getId();
            } else {
                $storeToFilter[] = $store;
            }
        } else {
            $storeToFilter = $store;
        }

        if ($withDefaultStore) {
            $storeToFilter[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $storeToFilter], 'public');

        return $this;
    }

    /**
     * Add product filter to collection
     *
     * @param int[]|int $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $productIdArray = [];
        if (!is_array($productId)) {
            $productIdArray[] = $productId;
        } else {
            $productIdArray = $productId;
        }

        $this->addFilter(ArticleInterface::PRODUCT_IDS, ['in' => $productIdArray], 'public');

        return $this;
    }

    /**
     * Add category enable filter to collection
     *
     * @param bool $isCategoryEnable
     * @return $this
     */
    public function addCategoryEnableFilter($isCategoryEnable)
    {
        $this->addFilter(ArticleInterface::IS_CATEGORY_ENABLE, $isCategoryEnable, 'public');

        return $this;
    }

    /**
     * Join store relation table
     *
     * @return void
     */
    protected function joinStoreRelationTable()
    {
        if ($this->getFilter('store')) {
            $this
                ->getSelect()
                ->join(
                    ['store_table' => $this->getTable('aw_faq_article_store')],
                    'main_table.article_id = store_table.article_id',
                    []
                )
                ->group(
                    'main_table.article_id'
                );
        }
    }

    /**
     * Join category relation table
     *
     * @return void
     */
    protected function joinCategoryRelationTable()
    {
        if ($this->getFilter(ArticleInterface::IS_CATEGORY_ENABLE)) {
            $this
                ->getSelect()
                ->join(
                    ['category_table' => $this->getTable(ResourceCategory::MAIN_TABLE_NAME)],
                    'main_table.category_id = category_table.category_id',
                    []
                )
                ->group(
                    'main_table.article_id'
                );
        }
    }

    /**
     * Join product article relation table
     *
     * @return void
     */
    protected function joinProductArticleRelationTable()
    {
        if ($this->getFilter(ArticleInterface::PRODUCT_IDS)) {
            $this
                ->getSelect()
                ->join(
                    ['product_article_table' => $this->getTable(ProductArticleRepository::PRODUCT_ARTICLE_TABLE_NAME)],
                    'main_table.article_id = product_article_table.article_id',
                    []
                )
                ->group(
                    'main_table.article_id'
                );
        }
    }
}

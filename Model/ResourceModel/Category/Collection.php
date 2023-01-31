<?php
namespace Aheadworks\FaqFree\Model\ResourceModel\Category;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as MagentoAbstractCollection;
use Magento\Store\Model\Store;

class Collection extends MagentoAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Category::class, CategoryResource::class);
        $this->_map['fields'][Category::CATEGORY_ID] = 'main_table.' . Category::CATEGORY_ID;
        $this->_map['fields']['store'] = 'store_table.' . Category::STORE_IDS;
    }

    /**
     * Returns pairs identifier - title for unique identifiersand pairs url-key|category_id - title for non-unique
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $result = [];
        $existingIdentifiers = [];
        foreach ($this as $item) {
            $urlKey = $item->getData(Category::URL_KEY);

            $data['value'] = $urlKey;
            $data['label'] = $item->getData(Category::NAME);

            if (in_array($urlKey, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData(Category::CATEGORY_ID);
            } else {
                $existingIdentifiers[] = $urlKey;
            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * Set additional data to category
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this
            ->attachStore()
            ->attachArticleCount();
        return parent::_afterLoad();
    }

    /**
     * Attach store to collection items
     *
     * @return $this
     */
    protected function attachStore()
    {
        $linkField = Category::CATEGORY_ID;

        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['faq' => $this->getTable('aw_faq_category_store')])
                ->where('faq.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $resultData = [];
                foreach ($result as $resultItem) {
                    $resultData[$resultItem[$linkField]][] = $resultItem[Category::STORE_IDS];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($resultData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $resultData[$linkedId]);
                    $item->setData(Category::STORE_IDS, $resultData[$linkedId]);
                }
            }
        }
        return $this;
    }

    /**
     * Attach article count
     *
     * @return $this
     */
    protected function attachArticleCount()
    {
        $linkField = Category::CATEGORY_ID;

        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(
                    ['faq_article' => $this->getTable('aw_faq_article')],
                    ['COUNT('. ArticleInterface::ARTICLE_ID . ')', $linkField]
                )->where('faq_article.' . $linkField . ' IN (?)', $linkedIds)
                ->group($linkField);
            $result = $connection->fetchAll($select);
            if ($result) {
                $resultData = [];
                foreach ($result as $resultItem) {
                    $resultData[$resultItem[$linkField]] = $resultItem['COUNT(article_id)'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($resultData[$linkedId])) {
                        continue;
                    }
                    $item->setData('article_count', $resultData[$linkedId]);
                }
            }
        }
        return $this;
    }

    /**
     * Join store table
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable();
        parent::_renderFiltersBefore();
    }

    /**
     * Add store field to filter
     *
     * @param string $field
     * @param mixed $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === Category::STORE_IDS) {
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
                    ['store_table' => $this->getTable('aw_faq_category_store')],
                    'main_table.category_id = store_table.category_id',
                    []
                )
                ->group(
                    'main_table.category_id'
                );
        }
    }
}

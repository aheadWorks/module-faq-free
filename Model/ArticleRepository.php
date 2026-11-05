<?php
namespace Aheadworks\FaqFree\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\FaqFree\Api\Data;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;
use Aheadworks\FaqFree\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Aheadworks\FaqFree\Model\ResourceModel\Article\CollectionFactory as ArticleCollectionFactory;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Collection;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Article as ArticleModel;
use Aheadworks\FaqFree\Model\Article\Converter as ArticleConverter;

class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * @var ResourceArticle
     */
    private $resource;

    /**
     * @var ArticleFactory
     */
    private $articleFactory;

    /**
     * @var ArticleCollectionFactory
     */
    private $articleCollectionFactory;

    /**
     * @var Data\ArticleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ArticleConverter
     */
    private $articleConverter;

    /**
     * @var ArticleInterface[]
     */
    private $instancesById = [];

    /**
     * ArticleRepository constructor.
     * @param ResourceArticle $resource
     * @param ArticleFactory $articleFactory
     * @param ArticleCollectionFactory $articleCollectionFactory
     * @param Data\ArticleSearchResultsInterfaceFactory $searchResultsFactory
     * @param ArticleConverter $articleConverter
     */
    public function __construct(
        ResourceArticle $resource,
        ArticleFactory $articleFactory,
        ArticleCollectionFactory $articleCollectionFactory,
        Data\ArticleSearchResultsInterfaceFactory $searchResultsFactory,
        ArticleConverter $articleConverter
    ) {
        $this->resource = $resource;
        $this->articleFactory = $articleFactory;
        $this->articleCollectionFactory = $articleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->articleConverter = $articleConverter;
    }

    /**
     * Save article
     *
     * @param ArticleInterface $article
     * @return ArticleInterface
     * @throws LocalizedException
     */
    public function save(ArticleInterface $article)
    {
        try {
            $this->resource->save($article);
            $this->instancesById[$this->getInstanceKey($article->getArticleId())] = $article;

        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the article: %1', $exception->getMessage())
            );
        }
        return $article;
    }

    /**
     * Retrieve article
     *
     * @param int $articleId
     * @param bool $forceLoad
     * @return ArticleInterface
     * @throws LocalizedException
     */
    public function getById($articleId, $forceLoad = false)
    {
        $instanceKey = $this->getInstanceKey($articleId);
        if ($forceLoad || !isset($this->instancesById[$instanceKey])) {
            /** @var Article|ArticleInterface $article */
            $article = $this->articleFactory->create();
            $this->resource->load($article, $articleId);
            if (!$article->getArticleId()) {
                throw new NoSuchEntityException(__('FAQ Article with id "%1" does not exist.', $articleId));
            }
            $this->instancesById[$instanceKey] = $article;
        }
        return $this->instancesById[$instanceKey];
    }

    /**
     * Retrieve articles matching the specified criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return ArticleSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var Collection $collection */
        $collection = $this->articleCollectionFactory->create();

        /** @var FilterGroup $filterGroup */
        $filterGroups = $criteria->getFilterGroups();
        if ($filterGroups) {
            foreach ($filterGroups as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
        }

        /** @var SortOrder $sortOrder */
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() === SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        }

        if ($criteria->getPageSize()) {
            $collection->setPageSize($criteria->getPageSize());
        }

        $items = [];
        /** @var ArticleModel $articleModel */
        foreach ($collection->getItems() as $articleModel) {
            $items[] = $this->articleConverter->toDataObject($articleModel);
        }

        /** @var ArticleSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($criteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Delete article
     *
     * @param ArticleInterface $article
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ArticleInterface $article)
    {
        try {
            $this->resource->delete($article);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the article: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * Delete article by ID
     *
     * @param int $articleId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($articleId)
    {
        return $this->delete($this->getById($articleId));
    }

    /**
     * Normalize instance cache key for PHP 8.4+ (null array offsets are deprecated)
     *
     * @param int|string|null $articleId
     * @return int|string
     */
    private function getInstanceKey($articleId)
    {
        return $articleId ?? '';
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    private function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            if ($filter->getField() == 'store_ids') {
                $collection->addStoreFilter($filter->getValue());
                continue;
            }
            if ($filter->getField() == ArticleInterface::PRODUCT_IDS) {
                $collection->addProductFilter($filter->getValue());
                continue;
            }
            if ($filter->getField() == ArticleInterface::IS_CATEGORY_ENABLE) {
                $collection->addCategoryEnableFilter($filter->getValue());
                continue;
            }
            $condition = $filter->getConditionType() ?: 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}

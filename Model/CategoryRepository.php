<?php
namespace Aheadworks\FaqFree\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\Search\FilterGroup;
use Aheadworks\FaqFree\Api\Data;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;
use Aheadworks\FaqFree\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Aheadworks\FaqFree\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Category\Collection;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Category\Converter as CategoryConverter;
use Magento\Framework\Exception\LocalizedException;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var ResourceCategory
     */
    private $resource;

    /**
     * @var ResourceArticle
     */
    private $resourceArticle;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var Data\CategorySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var CategoryConverter
     */
    private $categoryConverter;

    /**
     * @var CategoryInterface[]
     */
    private $instancesById = [];

    /**
     * CategoryRepository constructor.
     * @param ResourceCategory $resource
     * @param ResourceArticle $resourceArticle
     * @param CategoryFactory $categoryFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
     * @param ImageUploader $imageUploader
     * @param CategoryConverter $categoryConverter
     */
    public function __construct(
        ResourceCategory $resource,
        ResourceArticle $resourceArticle,
        CategoryFactory $categoryFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Data\CategorySearchResultsInterfaceFactory $searchResultsFactory,
        ImageUploader $imageUploader,
        CategoryConverter $categoryConverter
    ) {
        $this->resource = $resource;
        $this->resourceArticle = $resourceArticle;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->imageUploader = $imageUploader;
        $this->categoryConverter = $categoryConverter;
    }

    /**
     * Save category
     *
     * @param CategoryInterface $category
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function save(CategoryInterface $category)
    {
        try {
            $this->resource->save($category);
            $this->instancesById[$category->getCategoryId()] = $category;

            if ($category->getCategoryIcon()) {
                $category->setCategoryIcon($this->imageUploader->moveFileFromTmp($category->getCategoryIcon()));
            }

            if ($category->getArticleListIcon()) {
                $category->setArticleListIcon(
                    $this->imageUploader->moveFileFromTmp($category->getArticleListIcon())
                );
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the category: %1',
                $exception->getMessage()
            ));
        }
        return $category;
    }

    /**
     * Retrieve category
     *
     * @param int $categoryId
     * @param bool $forceLoad
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function getById($categoryId, $forceLoad = false)
    {
        if ($forceLoad || !isset($this->instancesById[$categoryId])) {
            /** @var Category|CategoryInterface $category */
            $category = $this->categoryFactory->create();
            $this->resource->load($category, $categoryId);
            if (!$category->getCategoryId()) {
                throw new NoSuchEntityException(__('FAQ Category with id "%1" does not exist.', $categoryId));
            }
            $this->instancesById[$categoryId] = $category;
        }
        return $this->instancesById[$categoryId];
    }

    /**
     * Retrieve categories matching the specified criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var Collection $collection */
        $collection = $this->categoryCollectionFactory->create();

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

        $items = [];
        if ($collection->getSize()) {
            while ($category = $collection->fetchItem()) {
                $items[] = $this->categoryConverter->toDataObject($category);
            }
        }

        /** @var CategorySearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($criteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Delete category
     *
     * @param CategoryInterface $category
     * @return bool
     * @throws LocalizedException
     */
    public function delete(CategoryInterface $category)
    {
        try {
            $this->resourceArticle->unsetCategoryIdFromArticles($category->getCategoryId());
            $this->resource->delete($category);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the category: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete category by ID
     *
     * @param int $categoryId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($categoryId)
    {
        $this->removeAllChild($categoryId);
        return $this->delete($this->getById($categoryId));
    }

    /**
     * Helper function that adds a FilterGroup to the collection
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
            $condition = $filter->getConditionType() ?: 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Removes all child categories from parent
     *
     * @param int $categoryId
     * @throws NoSuchEntityException
     */
    private function removeAllChild($categoryId)
    {
        $childCategories = $this->resource->getAllChildCategories($categoryId);
        if (isset($childCategories) && count($childCategories)) {
            foreach ($childCategories as $childCategory) {
                $childCategoryId = $childCategory[CategoryInterface::CATEGORY_ID];
                $categoryModel = $this->getById($childCategoryId);
                $this->delete($categoryModel);
            }
        }
    }
}

<?php
namespace Aheadworks\FaqFree\Model\Category;

use Aheadworks\FaqFree\Api\CategoryManagementInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;
use Aheadworks\FaqFree\Model\CategoryTree;
use Aheadworks\FaqFree\Model\CategoryTreeFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;

class Manager implements CategoryManagementInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var CategoryTreeFactory
     */
    private $categoryTreeFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CategoryRepository $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param ArticleRepository $articleRepository
     * @param CategoryTreeFactory $categoryTreeFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ArticleRepository $articleRepository,
        CategoryTreeFactory $categoryTreeFactory,
        LoggerInterface $logger
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->articleRepository = $articleRepository;
        $this->categoryTreeFactory = $categoryTreeFactory;
        $this->logger = $logger;
    }

    /**
     * Build category tree with articles (from tree top if root category not defined)
     *
     * @param int $storeId
     * @param CategoryInterface|null $category
     * @return CategoryTree
     */
    public function buildCategoryTree($storeId, $category = null)
    {
        $childTrees = [];
        $childrenParentId = $category ? $category->getCategoryId() : CategoryInterface::ROOT_CATEGORY_PARENT_ID ;

        /** @var SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder
            ->setField(CategoryInterface::SORT_ORDER)
            ->setAscendingDirection()
            ->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $storeId)
            ->addFilter(CategoryInterface::PARENT_ID, $childrenParentId)
            ->addSortOrder($sortOrder)
            ->create();

        try {
            /** @var CategoryInterface[] $categories */
            $childCategories = $this->categoryRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            $childCategories = [];
        }

        foreach ($childCategories as $childCategory) {
            $childTrees[] = $this->buildCategoryTree($storeId, $childCategory);
        }

        /** @var CategoryTree $resultTree */
        $resultTree = $this->categoryTreeFactory
            ->create()
            ->setCategory($category)
            ->setArticles($category ? $this->getCategoryArticles($category, $storeId) : [])
            ->setChildren($childTrees);

        return $resultTree;
    }

    /**
     * Retrieve articles for category
     *
     * @param CategoryInterface $category
     * @param int $storeId
     * @return ArticleInterface[]
     */
    public function getCategoryArticles($category, $storeId)
    {
        $categoryId = $category->getCategoryId();

        /** SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder
            ->setField(ArticleInterface::SORT_ORDER)
            ->setAscendingDirection()
            ->create();
        /** SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ArticleInterface::CATEGORY_ID, $categoryId)
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $storeId)
            ->setSortOrders([$sortOrder])
            ->create();

        try {
            /** @var ArticleInterface[] $result */
            $result = $this->articleRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            $result = [];
        }

        return $result;
    }

    /**
     * Check if article is descendant of category
     *
     * @param ArticleInterface $article
     * @param CategoryInterface $supposedAncestor
     * @return bool
     */
    public function isArticleDescendantOfCategory($article, $supposedAncestor)
    {
        $result = false;

        try {
            /** @var CategoryInterface $articleCategory */
            $articleCategory = $this->categoryRepository->getById($article->getCategoryId());
            $articleCategoryPathIds = explode('/', $articleCategory->getPath());
            if (in_array($supposedAncestor->getCategoryId(), $articleCategoryPathIds)) {
                $result = true;
            }
        } catch (LocalizedException $e) {} // phpcs:ignore

        return $result;
    }

    /**
     * Check if category is descendant of category
     *
     * @param CategoryInterface $category
     * @param CategoryInterface $supposedAncestor
     * @return bool
     */
    public function isCategoryDescendantOfCategory($category, $supposedAncestor)
    {
        $result = false;

        $categoryPathParts = explode('/', $category->getPath());
        if (in_array($supposedAncestor->getCategoryId(), $categoryPathParts)) {
            $result = true;
        }

        return $result;
    }
}

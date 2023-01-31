<?php
namespace Aheadworks\FaqFree\Block\Category;

use Aheadworks\FaqFree\Block\AbstractTemplate;
use Aheadworks\FaqFree\Model\Article as ArticleModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Category as CategoryModel;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;

class CategoryList extends AbstractTemplate implements IdentityInterface
{
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var array
     */
    private $moreArticleList = [];

    /**
     * @param Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository,
        SortOrderBuilder $sortOrderBuilder,
        Url $url,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $url, $config, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Retrieve categories
     *
     * @param int $parentId
     * @return array
     */
    public function getCategories($parentId = 0)
    {
        /** \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder
            ->setField(CategoryInterface::SORT_ORDER)
            ->setAscendingDirection()
            ->create();

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $this->getCurrentStore())
            ->addFilter(CategoryInterface::PARENT_ID, $parentId)
            ->addSortOrder($sortOrder)
            ->create();

        /** @var CategorySearchResultsInterface $articleList */
        $searchResults = $this->categoryRepository->getList($searchCriteria);

        return $searchResults->getItems();
    }

    /**
     * Retrieve articles of one category
     *
     * @param CategoryInterface|CategoryModel $category
     * @return array
     */
    public function getCategoryArticles($category)
    {
        $categoryId = $category->getCategoryId();
        $limit = $category->getNumArticlesToDisplay() ? $category->getNumArticlesToDisplay() : 0;

        /** \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder
            ->setField(ArticleInterface::SORT_ORDER)
            ->setAscendingDirection()
            ->create();
        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('category_id', $categoryId)
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $this->getCurrentStore())
            ->setPageSize($limit)
            ->setSortOrders([$sortOrder])
            ->create();

        /** @var ArticleSearchResultsInterface $articleList */
        $searchResults = $this->articleRepository->getList($searchCriteria);

        $this->moreArticleList[$category->getId()] =
            $searchResults->getTotalCount() - count($searchResults->getItems());

        return $searchResults->getItems();
    }

    /**
     * Get "Read more" number of articles
     *
     * @param int $categoryId
     * @return int
     */
    public function getMoreArticlesNumber($categoryId)
    {
        return isset($this->moreArticleList[$categoryId])
            ? $this->moreArticleList[$categoryId]
            : 0;
    }

    /**
     * Returns identities
     *
     * @return array
     */
    public function getIdentities()
    {
        try {
            /** @var ArticleInterface[] $allArticles */
            $allArticles = $this->articleRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        } catch (LocalizedException $e) {
            $allArticles = [];
        }

        try {
            /** @var CategoryInterface[] $allCategories */
            $allCategories = $this->categoryRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        } catch (LocalizedException $e) {
            $allCategories = [];
        }

        $identities = [];
        foreach ($allArticles as $article) {
            $identities[] = ArticleModel::CACHE_TAG . '_' . $article->getArticleId();
        }
        foreach ($allCategories as $category) {
            $identities[] = CategoryModel::CACHE_TAG . '_' . $category->getCategoryId();
        }

        return array_unique($identities);
    }
}

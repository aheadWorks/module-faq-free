<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\FaqFree\Api\SearchManagementInterface;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterfaceFactory;
use Aheadworks\FaqFree\Model\ResourceModel\Search as SearchResource;
use Aheadworks\FaqFree\Model\ResourceModel\Category as CategoryResource;
use Aheadworks\FaqFree\Model\SearchQueryFormatter\FormatterInterface as SearchQueryFormatter;

class Search implements SearchManagementInterface
{
    /**
     * @var ArticleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SearchResource
     */
    private $searchResource;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var CategoryRepositoryInterface
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
     * @var SearchQueryFormatter
     */
    private $searchQueryFormatter;

    /**
     * @param ArticleSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchResource $searchResource
     * @param CategoryResource $categoryResource
     * @param ArticleRepository $articleRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchQueryFormatter $searchQueryFormatter
     */
    public function __construct(
        ArticleSearchResultsInterfaceFactory $searchResultsFactory,
        SearchResource $searchResource,
        CategoryResource $categoryResource,
        ArticleRepository $articleRepository,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        SearchQueryFormatter $searchQueryFormatter
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchResource = $searchResource;
        $this->categoryResource = $categoryResource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchQueryFormatter = $searchQueryFormatter;
    }

    /**
     * Make Full Text Search and return found Articles
     *
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @param bool $findByTag
     * @return ArticleSearchResultsInterface
     * @internal param SearchCriteriaInterface $searchCriteria
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function searchArticles($searchString, $storeId, $limit = null, $findByTag = false)
    {
        $searchString =  $findByTag ? $searchString : $this->searchQueryFormatter->format($searchString);
        $articles = $this->searchResource->searchQuery($searchString, $limit, $findByTag, $storeId);
        return $this->searchResultsFactory->create()->setItems($articles);
    }

    /**
     * Make Full Text Search and return found categories
     *
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @return CategorySearchResultsInterface
     */
    public function searchCategories($searchString, $storeId, $limit = null)
    {
        $searchString = $this->searchQueryFormatter->format($searchString);

        if (!$searchString || empty($categoryIds = $this->categoryResource->searchQuery($searchString, $limit))) {
            return $this->searchResultsFactory->create()->setItems([]);
        }

        /** \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::STORE_IDS, $storeId, 'in')
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::CATEGORY_ID, $categoryIds, 'in')
            ->create();

        return $this->categoryRepository->getList($searchCriteria);
    }
}

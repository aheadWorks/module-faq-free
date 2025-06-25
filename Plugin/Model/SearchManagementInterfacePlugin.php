<?php
namespace Aheadworks\FaqFree\Plugin\Model;

use Aheadworks\FaqFree\Api\SearchManagementInterface;
use Aheadworks\FaqFree\Model\Validator\SearchQuery\AbstractValidator as SearchQueryValidator;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface as ArticleSearchResults;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterfaceFactory as ArticleSearchResultsFactory;
use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterface as CategorySearchResults;
use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterfaceFactory as CategorySearchResultsFactory;
use Aheadworks\FaqFree\Model\Validator\SearchQuery\SearchQueryDataObject;
use Aheadworks\FaqFree\Model\Validator\SearchQuery\SearchQueryDataObjectFactory;

class SearchManagementInterfacePlugin
{
    /**
     * @var SearchQueryValidator
     */
    private $searchQueryValidator;

    /**
     * @var ArticleSearchResultsFactory
     */
    private $articleSearchResultsFactory;

    /**
     * @var CategorySearchResultsFactory
     */
    private $categorySearchResultsFactory;

    /**
     * @var SearchQueryDataObjectFactory
     */
    private $searchQueryDataObjectFactory;

    /**
     * @param SearchQueryValidator $searchQueryValidator
     * @param ArticleSearchResultsFactory $articleSearchResultsFactory
     * @param CategorySearchResultsFactory $categorySearchResultsFactory
     * @param SearchQueryDataObjectFactory $searchQueryDataObjectFactory
     */
    public function __construct(
        SearchQueryValidator $searchQueryValidator,
        ArticleSearchResultsFactory $articleSearchResultsFactory,
        CategorySearchResultsFactory $categorySearchResultsFactory,
        SearchQueryDataObjectFactory $searchQueryDataObjectFactory
    ) {
        $this->searchQueryValidator = $searchQueryValidator;
        $this->articleSearchResultsFactory = $articleSearchResultsFactory;
        $this->categorySearchResultsFactory = $categorySearchResultsFactory;
        $this->searchQueryDataObjectFactory = $searchQueryDataObjectFactory;
    }

    /**
     * Validates search articles request
     *
     * @param SearchManagementInterface $subject
     * @param callable $proceed
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @param bool $findByTag
     * @return ArticleSearchResults
     */
    public function aroundSearchArticles(
        SearchManagementInterface $subject,
        callable $proceed,
        string $searchString,
        int $storeId,
        ?int $limit = null,
        bool $findByTag = false
    ) {
        /** @var SearchQueryDataObject $searchQueryDataObject */
        $searchQueryDataObject = $this->searchQueryDataObjectFactory
            ->create()
            ->setQueryString($searchString)
            ->setStoreId($storeId);

        if ($findByTag || $this->searchQueryValidator->isValid($searchQueryDataObject)) {
            $result = $proceed($searchString, $storeId, $limit, $findByTag);
        } else {
            /** @var ArticleSearchResults $emptyResult */
            $result = $this->articleSearchResultsFactory->create();
            $result->setItems([]);
        }

        return $result;
    }

    /**
     * Validates search categories request
     *
     * @param SearchManagementInterface $subject
     * @param callable $proceed
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @return CategorySearchResults
     */
    public function aroundSearchCategories(
        SearchManagementInterface $subject,
        callable $proceed,
        string $searchString,
        int $storeId,
        ?int $limit = null
    ) {
        /** @var SearchQueryDataObject $searchQueryDataObject */
        $searchQueryDataObject = $this->searchQueryDataObjectFactory
            ->create()
            ->setQueryString($searchString)
            ->setStoreId($storeId);

        if ($this->searchQueryValidator->isValid($searchQueryDataObject)) {
            $result = $proceed($searchString, $storeId, $limit);
        } else {
            /** @var CategorySearchResults $emptyResult */
            $result = $this->categorySearchResultsFactory->create();
            $result->setItems([]);
        }

        return $result;
    }
}

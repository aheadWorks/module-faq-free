<?php
namespace Aheadworks\FaqFree\Model\Article\Listing;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface as ArticleRepository;

class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * Builder constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ArticleRepository $articleRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Returns articles list
     *
     * @return \Aheadworks\FaqFree\Api\Data\ArticleInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getArticleList()
    {
        return $this->articleRepository
            ->getList($this->buildSearchCriteria())
            ->getItems();
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Build search criteria
     *
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}

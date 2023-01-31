<?php

namespace Aheadworks\FaqFree\Api;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * FAQ article CRUD interface
 *
 * @api
 */
interface ArticleRepositoryInterface
{
    /**
     * Save article
     *
     * @param ArticleInterface $article
     * @return ArticleInterface
     * @throws LocalizedException
     */
    public function save(ArticleInterface $article);

    /**
     * Retrieve article
     *
     * @param int $articleId
     * @param bool $forceLoad
     * @return ArticleInterface
     * @throws LocalizedException
     */
    public function getById($articleId, $forceLoad = false);

    /**
     * Retrieve articles matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ArticleSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete article
     *
     * @param ArticleInterface $article
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ArticleInterface $article);

    /**
     * Delete article by ID
     *
     * @param int $articleId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($articleId);
}

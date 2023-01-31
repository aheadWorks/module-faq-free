<?php
namespace Aheadworks\FaqFree\Api;

use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface;

/**
 * FAQ search interface
 *
 * @api
 */
interface SearchManagementInterface
{
    /**
     * Make Full Text Search and return found Articles
     *
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @param bool $findByTag
     * @return ArticleSearchResultsInterface
     * @internal param SearchCriteriaInterface $searchCriteria
     */
    public function searchArticles($searchString, $storeId, $limit = null, $findByTag = false);

    /**
     * Make Full Text Search and return found categories
     *
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @return CategorySearchResultsInterface
     */
    public function searchCategories($searchString, $storeId, $limit = null);
}

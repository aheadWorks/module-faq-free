<?php
namespace Aheadworks\FaqFree\Api;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\CategoryTree;

/**
 * Interface CategoryManagementInterface
 */
interface CategoryManagementInterface
{
    /**
     * Build category tree with articles (from tree top if root category not defined)
     *
     * @param int $storeId
     * @param CategoryInterface $rootCategory
     * @return CategoryTree
     */
    public function buildCategoryTree($storeId, $rootCategory = null);

    /**
     * Retrieve articles for category
     *
     * @param CategoryInterface $category
     * @param int $storeId
     * @return ArticleInterface[]
     */
    public function getCategoryArticles($category, $storeId);

    /**
     * Check if article is descendant of category
     *
     * @param ArticleInterface $article
     * @param CategoryInterface $supposedAncestor
     * @return bool
     */
    public function isArticleDescendantOfCategory($article, $supposedAncestor);

    /**
     * Check if category is descendant of category
     *
     * @param CategoryInterface $category
     * @param CategoryInterface $supposedAncestor
     * @return bool
     */
    public function isCategoryDescendantOfCategory($category, $supposedAncestor);
}

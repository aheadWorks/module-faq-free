<?php

namespace Aheadworks\FaqFree\Api;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * FAQ Category CRUD interface
 *
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save category
     *
     * @param CategoryInterface $category
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function save(CategoryInterface $category);

    /**
     * Retrieve category
     *
     * @param int $categoryId
     * @param bool $forceLoad
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function getById($categoryId, $forceLoad = false);

    /**
     * Retrieve categories matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CategorySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete category
     *
     * @param CategoryInterface $category
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CategoryInterface $category);

    /**
     * Delete category by ID
     *
     * @param int $categoryId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($categoryId);
}

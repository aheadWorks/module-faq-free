<?php
namespace Aheadworks\FaqFree\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Tag CRUD interface
 * @api
 */
interface TagRepositoryInterface
{
    /**
     * Save tag
     *
     * @param \Aheadworks\FaqFree\Api\Data\TagInterface $tag
     * @return \Aheadworks\FaqFree\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\FaqFree\Api\Data\TagInterface $tag);

    /**
     * Retrieve tag
     *
     * @param int $tagId
     * @return \Aheadworks\FaqFree\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tagId);

    /**
     * Retrieve tags matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\FaqFree\Api\Data\TagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete tag
     *
     * @param \Aheadworks\FaqFree\Api\Data\TagInterface $tag
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\FaqFree\Api\Data\TagInterface $tag);

    /**
     * Delete tag by ID
     *
     * @param int $tagId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tagId);
}

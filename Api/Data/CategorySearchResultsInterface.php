<?php

namespace Aheadworks\FaqFree\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for FAQ category search results
 *
 * @api
 */
interface CategorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get categories list
     *
     * @return \Aheadworks\FaqFree\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set articles list
     *
     * @param \Aheadworks\FaqFree\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

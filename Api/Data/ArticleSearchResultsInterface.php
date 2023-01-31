<?php

namespace Aheadworks\FaqFree\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for FAQ article search results
 *
 * @api
 */
interface ArticleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get articles list
     *
     * @return \Aheadworks\FaqFree\Api\Data\ArticleInterface[]
     */
    public function getItems();

    /**
     * Set articles list
     *
     * @param \Aheadworks\FaqFree\Api\Data\ArticleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

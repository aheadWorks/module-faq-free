<?php
namespace Aheadworks\FaqFree\Model\SearchQueryFormatter;

interface FormatterInterface
{
    /**
     * Prepare Search Query
     *
     * @param string $searchQuery
     * @return string
     */
    public function format($searchQuery);
}

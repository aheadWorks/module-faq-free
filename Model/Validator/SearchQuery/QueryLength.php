<?php
namespace Aheadworks\FaqFree\Model\Validator\SearchQuery;

use Aheadworks\FaqFree\Model\Config as FaqConfig;

class QueryLength extends AbstractValidator
{
    /**
     * @var FaqConfig
     */
    private $faqConfig;

    /**
     * @param FaqConfig $faqConfig
     */
    public function __construct(
        FaqConfig $faqConfig
    ) {
        $this->faqConfig = $faqConfig;
    }

    /**
     * Validate search query length
     *
     * @param SearchQueryDataObject $searchQuery
     * @return bool
     */
    public function isValid($searchQuery)
    {
        $this->_clearMessages();

        $queryString = $searchQuery->getQueryString();
        $storeId = $searchQuery->getStoreId();

        $searchQueryMinLength = $this->faqConfig->getMinSearchQueryLength($storeId);
        $searchQueryMaxLength = $this->faqConfig->getMaxSearchQueryLength($storeId);
        $searchQueryLength = strlen($queryString);

        if ($searchQueryLength === 0) {
            $message = __('Search query cannot be empty');
            $this->_addMessages([$message]);
        }

        if ($searchQueryMinLength && $searchQueryLength < $searchQueryMinLength) {
            $message = __('Minimum Search query length is %1', $searchQueryMinLength);
            $this->_addMessages([$message]);
        }

        if ($searchQueryMaxLength && $searchQueryLength > $searchQueryMaxLength) {
            $message = __('Maximum Search query length is %1', $searchQueryMaxLength);
            $this->_addMessages([$message]);
        }

        return empty($this->getMessages());
    }
}

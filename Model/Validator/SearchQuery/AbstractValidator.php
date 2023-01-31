<?php
namespace Aheadworks\FaqFree\Model\Validator\SearchQuery;

abstract class AbstractValidator extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * Returns true if and only if $searchQuery meets the validation requirements
     *
     * @param SearchQueryDataObject $searchQuery
     * @return bool
     */
    abstract public function isValid($searchQuery);
}

<?php
namespace Aheadworks\FaqFree\Model\Validator\SearchQuery;

class Composite extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param array $validators
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * Returns true if and only if $searchQuery meets the validation requirements
     *
     * @param SearchQueryDataObject $searchQuery
     * @return bool
     */
    public function isValid($searchQuery)
    {
        $this->_clearMessages();

        /** @var AbstractValidator $validator */
        foreach ($this->validators as $validator) {
            if (!$validator->isValid($searchQuery)) {
                $messages = $validator->getMessages();
                $this->_addMessages($messages);
            }
        }

        return empty($this->getMessages());
    }
}

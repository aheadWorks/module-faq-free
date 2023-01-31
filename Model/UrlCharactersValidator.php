<?php
namespace Aheadworks\FaqFree\Model;

use Magento\Framework\Validator\AbstractValidator;

class UrlCharactersValidator extends AbstractValidator
{
    /**
     * Is valid url
     *
     * @param string $urlCharactersString
     * @return bool
     */
    public function isValid($urlCharactersString)
    {
        $this->_clearMessages();

        if (!empty($urlCharactersString) && !preg_match('/^[a-z0-9_.\/-]+$/', $urlCharactersString)) {
            $message = __('String contains disallowed characters');
            $this->_addMessages([$message]);
        }

        return empty($this->getMessages());
    }
}

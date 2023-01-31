<?php

namespace Aheadworks\FaqFree\Model\Article;

use \Magento\Framework\Validator\AbstractValidator;
use \Aheadworks\FaqFree\Model\UrlKeyValidator;
use \Aheadworks\FaqFree\Model\Article;

/**
 * FAQ Article Validator
 */
class Validator extends AbstractValidator
{
    /**
     * @var UrlKeyValidator
     */
    private $urlKeyValidator;

    /**
     * @param UrlKeyValidator $urlKeyValidator
     */
    public function __construct(UrlKeyValidator $urlKeyValidator)
    {
        $this->urlKeyValidator = $urlKeyValidator;
    }

    /**
     * Validate article data
     *
     * @param Article $article
     * @return bool     Return FALSE if someone item is invalid
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function isValid($article)
    {
        $errors = [];

        if (empty($article->getTitle())) {
            $errors['title'] = __('Title can\'t be empty.');
        }

        if (empty($article->getUrlKey())) {
            $errors['url_key'] = __('Url key can\'t be empty.');
        }

        if ($article->getSortOrder() && !is_numeric($article->getSortOrder())) {
            $errors['sort_order'] = __('Sort order must contain only digits.');
        }

        if (!$this->urlKeyValidator->isValid($article)) {
            $errors = array_merge($errors, $this->urlKeyValidator->getMessages());
        }

        $this->_addMessages($errors);

        return empty($errors);
    }
}

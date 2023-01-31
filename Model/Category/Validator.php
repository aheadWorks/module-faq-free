<?php

namespace Aheadworks\FaqFree\Model\Category;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\FaqFree\Model\UrlKeyValidator;
use Aheadworks\FaqFree\Model\Category;

/**
 * FAQ Category Validator
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
     * Validate Edit Category form fields
     *
     * Return FALSE if someone item is invalid
     *
     * @param Category $category
     * @return bool
     */
    public function isValid($category)
    {
        $errors = [];

        if (empty($category->getName())) {
            $errors['title'] = __('Title can\'t be empty.');
        }

        if (empty($category->getUrlKey())) {
            $errors['url_key'] = __('Url key can\'t be empty.');
        }

        if ($category->getSortOrder() && !is_numeric($category->getSortOrder())) {
            $errors['sort_order'] = __('Sort order must contain only digits.');
        }

        if ($category->getNumArticlesToDisplay() && !is_numeric($category->getNumArticlesToDisplay())
        ) {
            $errors['num_articles'] = __('Number of articles to display must contain only digits.');
        }

        if (!$this->urlKeyValidator->isValid($category)) {
            $errors = array_merge($errors, $this->urlKeyValidator->getMessages());
        }

        $this->_addMessages($errors);

        return empty($errors);
    }
}

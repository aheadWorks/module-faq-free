<?php
namespace Aheadworks\FaqFree\Block;

use Magento\Framework\View\Element\Template;
use Aheadworks\FaqFree\ViewModel\Article\Tags as ArticleTagsViewModel;

abstract class AbstractArticleTags extends Template
{
    /**
     * Retrieve article instance id
     *
     * @return int
     */
    abstract public function getArticleId();
}

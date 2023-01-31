<?php
namespace Aheadworks\FaqFree\Block\Product;

use Aheadworks\FaqFree\Block\AbstractArticleTags;

class ArticleTags extends AbstractArticleTags
{
    /**
     * Set article id
     *
     * @param int $articleId
     * @return $this
     */
    public function setArticleId($articleId)
    {
        $this->setData('articleId', $articleId);

        return $this;
    }

    /**
     * Retrieve article instance id
     *
     * @return int
     */
    public function getArticleId()
    {
        return $this->getData('articleId');
    }
}

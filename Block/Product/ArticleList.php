<?php
namespace Aheadworks\FaqFree\Block\Product;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject\IdentityInterface;

class ArticleList extends Template implements IdentityInterface
{
    /**
     * Returns article list item renderer
     *
     * @param string $articleListItemRendererAlias
     * @param ArticleInterface $article
     * @return ArticleListItem|null
     */
    public function getArticleListItemRenderer($articleListItemRendererAlias, $article)
    {
        /** @var  ArticleListItem $articleListItemRenderer */
        $articleListItemRenderer = $this->getChildBlock($articleListItemRendererAlias);
        if ($articleListItemRenderer) {
            $articleListItemRenderer->setArticle($article);
        }

        return $articleListItemRenderer;
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->getViewModel()->isShowFaqProductTab()
            ? parent::toHtml()
            : '';
    }

    /**
     * Returns identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getViewModel()->getBlockIdentities();
    }

    /**
     * Preparing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setData('title', __('FAQ'));
        return parent::_prepareLayout();
    }
}

<?php
namespace Aheadworks\FaqFree\Block\Product;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\View\Element\Template;
use Aheadworks\FaqFree\ViewModel\Product\ArticleListItem as ArticleListItemViewModel;
use Aheadworks\FaqFree\Block\Product\ArticleTags as ArticleTagsBlock;

/**
 * @method ArticleListItemViewModel getViewModel()
 * @method $this setArticle(ArticleInterface $article)
 * @method ArticleInterface getArticle()
 */
class ArticleListItem extends Template
{
    /**
     * Returns article list item tags renderer
     *
     * @param string $articleTagsRendererAlias
     * @param ArticleInterface $article
     * @return ArticleTags
     */
    public function getArticleTagsRenderer($articleTagsRendererAlias, ArticleInterface $article)
    {
        /** @var ArticleTagsBlock $articleTagsRenderer */
        $articleTagsRenderer = $this->getChildBlock($articleTagsRendererAlias);
        if ($articleTagsRenderer) {
            $articleTagsRenderer->setArticleId($article->getArticleId());
        }

        return $articleTagsRenderer;
    }
}

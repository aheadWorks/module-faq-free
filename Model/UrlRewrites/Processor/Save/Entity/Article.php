<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Processor\Save\Entity;

use Aheadworks\FaqFree\Model\UrlRewrites\Service\Article as ArticleRewritesService;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\Exception\LocalizedException;

class Article
{
    /**
     * @var ArticleRewritesService
     */
    private $articleRewritesService;

    /**
     * Article constructor.
     * @param ArticleRewritesService $articleRewritesService
     */
    public function __construct(
        ArticleRewritesService $articleRewritesService
    ) {
        $this->articleRewritesService = $articleRewritesService;
    }

    /**
     * Process rewrites based on new and orig article state
     *
     * @param ArticleInterface $article
     * @param ArticleInterface|null $origArticle
     * @return bool
     * @throws LocalizedException
     */
    public function process($article, $origArticle = null)
    {
        if ($article && $origArticle) {
            $this->articleRewritesService->updatePermanentRedirects($article, $origArticle);
        }

        $this->articleRewritesService->updateControllerRewrites($article);

        return true;
    }
}

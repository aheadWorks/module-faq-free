<?php
declare(strict_types=1);

namespace Aheadworks\FaqFree\Block\Article;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\FaqFree\Model\ResourceModel\Article as Articles;

class Article extends Template implements IdentityInterface
{
    /**
     * Article Construct
     *
     * @param Context $context
     * @param ArticleRepositoryInterface $articleRepository
     * @param FilterProvider $filterProvider
     * @param Articles $articles
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly FilterProvider $filterProvider,
        private readonly Articles $articles,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve article instance
     *
     * @return null | ArticleInterface
     */
    public function getArticle()
    {
        $articleId = $this->getRequest()->getParam('id');

        if (!$articleId) {
            $path = $this->getRequest()->getOriginalPathInfo();
            $urlKey = ltrim(strrchr((trim($path, '/')), '/'), '/');
            $articleId = $this->articles->getIdByUrlKey($urlKey);
        }
        if ($articleId) {
            return $this->articleRepository->getById($articleId);
        }
        return null;
    }

    /**
     * Retrieve article title
     *
     * @return null | string
     */
    public function getTitle()
    {
        if ($this->getArticle()) {
            return $this->getArticle()->getTitle();
        }
        return null;
    }

    /**
     * Retrieve article content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->getArticle()->getContent());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getArticle()) {
            return $identities = $this->getArticle()->getIdentities();
        } else {
            return $identities;
        }
    }
}

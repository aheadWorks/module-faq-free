<?php
namespace Aheadworks\FaqFree\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\FaqFree\Model\Url;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;

class AbstractTemplate extends Template
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $url,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Get current stores
     *
     * @return int
     */
    protected function getCurrentStore()
    {
        return (int)$this->_storeManager->getStore()->getId();
    }

    /**
     * Retrieve icon URL for article list
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getArticleListIconUrl($category)
    {
        return $this->url->getArticleListIconUrl($category);
    }

    /**
     * Retrieve article URL
     *
     * @param ArticleInterface|Article $article
     * @return string
     */
    public function getArticleUrl($article)
    {
        return $this->url->getArticleUrl($article);
    }

    /**
     * Retrieve category icon URL
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getCategoryIconUrl($category)
    {
        return $this->url->getCategoryIconUrl($category);
    }

    /**
     * Retrieve category URL
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->url->getCategoryUrl($category);
    }

    /**
     * Retrieve FAQ Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->config->getFaqName();
    }

    /**
     * Retrieve FAQ Search results page url
     *
     * @return string
     */
    public function getFaqSearchUrl()
    {
        return $this->url->getSearchResultsPageUrl();
    }
}

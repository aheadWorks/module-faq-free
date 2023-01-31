<?php
namespace Aheadworks\FaqFree\Model;

use Aheadworks\FaqFree\Api\Data\TagInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;

/**
 * FAQ url model
 */
class Url
{
    /**
     * FAQ search route
     */
    public const FAQ_SEARCH_ROUTE = 'search';

    /**
     * FAQ tag route
     */
    public const FAQ_TAG_ROUTE = 'tag';

    /**
     * FAQ media path
     */
    public const MEDIA_PATH = 'faq';

    /**
     * FAQ search query parameter
     */
    public const FAQ_QUERY_PARAM = 'fq';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlInterface $url
     * @param Config $config
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlInterface $url,
        Config $config,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->url = $url;
        $this->config = $config;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve Store URL
     *
     * @param int $storeId
     * @return string
     */
    public function getBaseUrl($storeId = null)
    {
        return $storeId
            ? $this->storeManager->getStore($storeId)->getBaseUrl()
            : $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Retrieve FAQ route name without slashes
     *
     * @param int $storeId
     * @return string
     * @internal param StoreManagerInterface $store
     */
    public function getFaqRoute($storeId = null)
    {
        return trim((string)$this->config->getFaqRoute($storeId), '/');
    }

    /**
     * Retrieve full FAQ route name
     *
     * @param int $storeId
     * @return string
     * @internal param StoreManagerInterface $store
     */
    public function getFullFaqRoute($storeId = null)
    {
        return $this->config->getFaqRoute($storeId);
    }

    /**
     * Retrieve FAQ base url
     *
     * @param int|null $storeId
     * @return string
     */
    public function getFaqHomeUrl($storeId = null)
    {
        return $this->getBaseUrl($storeId) . $this->config->getFaqRoute();
    }

    /**
     * Retrieve FAQ category route
     *
     * @param CategoryInterface|Category $category
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryRoute($category, $storeId = null)
    {
        return $this->getFaqRoute($storeId) . '/' . $category->getUrlKey();
    }

    /**
     * Retrieve FAQ category route
     *
     * @param CategoryInterface|Category $category
     * @param int|null $storeId
     * @return string
     */
    public function getFullCategoryRoute($category, $storeId = null)
    {
        return $this->getFaqRoute($storeId) . '/' . $category->getUrlKey() . $this->config->getCategoryUrlSuffix();
    }

    /**
     * Retrieve FAQ article route
     *
     * @param ArticleInterface|Article $article
     * @param int|null $storeId
     * @return string
     * @throws LocalizedException
     */
    public function getArticleRoute($article, $storeId = null)
    {
        $categoryId = $article->getCategoryId();
        $category = $this->categoryRepository->getById($categoryId);
        return $this->getCategoryRoute($category, $storeId) . '/' . $article->getUrlKey();
    }

    /**
     * Retrieve full FAQ article route
     *
     * @param ArticleInterface|Article $article
     * @param int|null $storeId
     * @return string
     * @throws LocalizedException
     */
    public function getFullArticleRoute($article, $storeId = null)
    {
        return $this->getArticleRoute($article, $storeId) . $this->config->getArticleUrlSuffix();
    }

    /**
     * Retrieve FAQ category url
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->getBaseUrl() . $this->getCategoryRoute($category) . $this->config->getCategoryUrlSuffix();
    }

    /**
     * Retrieve FAQ category url without suffix
     *
     * @param CategoryInterface|Category $category
     * @param int $storeId
     * @return string
     */
    public function getClearCategoryUrl($category, $storeId = null)
    {
        return $this->getBaseUrl($storeId) . $this->getCategoryRoute($category);
    }

    /**
     * Retrieve FAQ article url
     *
     * @param ArticleInterface|Article $article
     * @param int $storeId
     * @return string
     */
    public function getArticleUrl($article, $storeId = null)
    {
        $categoryId = $article->getCategoryId();
        $category = $this->categoryRepository->getById($categoryId);

        $articleUrlStart = ($category->getIsEnable()
            ? $this->getClearCategoryUrl($category, $storeId)
            : $this->getFaqHomeUrl($storeId));

        return $articleUrlStart . '/' . $article->getUrlKey() . $this->config->getArticleUrlSuffix();
    }

    /**
     * Get url of category image icon
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryIconUrl(CategoryInterface $category)
    {
        return $this->getMediaUrl($category->getCategoryIcon());
    }

    /**
     * Get an image icon url for article listing
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getArticleListIconUrl(CategoryInterface $category)
    {
        return $this->getMediaUrl($category->getArticleListIcon());
    }

    /**
     * Retrieve FAQ media url
     *
     * @param string $mediaName
     * @return string
     */
    private function getMediaUrl($mediaName)
    {
        $baseMediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaName ? $baseMediaUrl . self::MEDIA_PATH . '/' . $mediaName : null;
    }

    /**
     * Retrieve FAQ search results page route
     *
     * @return string
     */
    public function getSearchResultsPageRoute()
    {
        return $this->config->getFaqRoute() . '/' . self::FAQ_SEARCH_ROUTE;
    }

    /**
     * Retrieve FAQ Search results page url
     *
     * @return string
     */
    public function getSearchResultsPageUrl()
    {
        return $this->getBaseUrl() . $this->getFaqRoute() . '/' . self::FAQ_SEARCH_ROUTE;
    }

    /**
     * Get search url by tag
     *
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchUrlByTag($tag)
    {
        $tagName = $tag instanceof TagInterface ? $tag->getName() : $tag;
        $faqRoute = $this->config->getFaqRoute();

        return $this->url->getDirectUrl($faqRoute . '/tag?' . Url::FAQ_QUERY_PARAM . '=' . $tagName);
    }
}

<?php
namespace Aheadworks\FaqFree\Model\Sitemap;

use Aheadworks\FaqFree\Api\ArticleRepositoryInterface;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sitemap\Model\SitemapItemInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;

class ItemsProvider
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SitemapItemInterfaceFactory
     */
    private $itemFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Url $url
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Url $url,
        ObjectManagerInterface $objectManager,
        Config $config
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->url = $url;
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Retrieve sitemap items
     *
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        $items = [];
        $items = array_merge($items, $this->getFaqHomePageItem($storeId));
        $items = array_merge($items, $this->getCategoryItems($storeId));
        $items = array_merge($items, $this->getArticleItems($storeId));

        return $items;
    }

    /**
     * Retrieves FAQ homepage sitemap item
     *
     * @param int $storeId
     * @return SitemapItemInterface[]
     */
    public function getFaqHomePageItem($storeId)
    {
        return [
            $this->getSitemapItem(
                [
                    'id' => 'faq_home',
                    'url' => $this->url->getFullFaqRoute($storeId),
                    'updatedAt' => $this->getCurrentDateTime(),
                    'priority' => $this->getPriority($storeId),
                    'changeFrequency' => $this->getChangeFreq($storeId)
                ]
            )
        ];
    }

    /**
     * Retrieves FAQ category sitemap items
     *
     * @param int $storeId
     * @return SitemapItemInterface[]
     */
    public function getCategoryItems($storeId)
    {
        $categoryItems = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $storeId)
            ->create();
        $categories = $this->categoryRepository->getList($searchCriteria)
            ->getItems();
        foreach ($categories as $category) {
            $categoryItems[$category->getCategoryId()] = $this->getSitemapItem(
                [
                    'id' => $category->getCategoryId(),
                    'url' => $this->url->getCategoryRoute($category, $storeId),
                    'updatedAt' => $this->getCurrentDateTime(),
                    'priority' => $this->getPriority($storeId),
                    'changeFrequency' => $this->getChangeFreq($storeId)
                ]
            );
        }
        return $categoryItems;
    }

    /**
     * Retrieves FAQ article sitemap items
     *
     * @param int $storeId
     * @return SitemapItemInterface[]
     */
    public function getArticleItems($storeId)
    {
        $articleItems = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $storeId)
            ->create();
        $articles = $this->articleRepository->getList($searchCriteria)
            ->getItems();
        foreach ($articles as $article) {
            $articleItems[$article->getArticleId()] = $this->getSitemapItem(
                [
                    'id' => $article->getArticleId(),
                    'url' => $this->url->getArticleRoute($article, $storeId),
                    'updatedAt' => $this->getCurrentDateTime(),
                    'priority' => $this->getPriority($storeId),
                    'changeFrequency' => $this->getChangeFreq($storeId)
                ]
            );
        }
        return $articleItems;
    }

    /**
     * Get change frequency
     *
     * @param int $storeId
     * @return float
     */
    private function getChangeFreq($storeId)
    {
        return $this->config->getSitemapChangeFrequency($storeId);
    }

    /**
     * Get priority
     *
     * @param int $storeId
     * @return string
     */
    private function getPriority($storeId)
    {
        return $this->config->getSitemapPriority($storeId);
    }

    /**
     * Current date/time
     *
     * @return string
     */
    private function getCurrentDateTime()
    {
        return (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * Retrieve sitemap item 2.3.x compatibility
     *
     * @param array $itemData
     * @return SitemapItemInterface
     */
    protected function getSitemapItem($itemData)
    {
        if (!$this->itemFactory) {
            $this->itemFactory = $this->objectManager->create(SitemapItemInterfaceFactory::class);
        }

        return $this->itemFactory->create($itemData);
    }
}

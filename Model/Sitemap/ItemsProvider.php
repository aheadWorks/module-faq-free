<?php
declare(strict_types=1);

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
     * Sitemap item factory class
     */
    private const SITEMAP_ITEM_FACTORY_CLASS = \Magento\Sitemap\Model\SitemapItemInterfaceFactory::class;

    /**
     * Category ids to filter in articles
     *
     * @var array
     */
    private $enabledCategoryIds = [];

    /**
     * @var SitemapItemInterfaceFactory
     */
    private $itemFactory;

    /**
     * ItemsProvider Construct
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Url $url
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Url $url,
        private readonly ObjectManagerInterface $objectManager,
        private readonly Config $config
    ) {
    }

    /**
     * Retrieve sitemap items
     *
     * @param int $storeId
     * @return array
     */
    public function getItems(int $storeId): array
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
    public function getFaqHomePageItem(int $storeId): array
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
    public function getCategoryItems(int $storeId): array
    {
        $categoryItems = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $storeId)
            ->create();
        $categories = $this->categoryRepository->getList($searchCriteria)
            ->getItems();
        foreach ($categories as $category) {
            $this->enabledCategoryIds[] = $category->getCategoryId();
            $categoryItems[$category->getCategoryId()] = $this->getSitemapItem(
                [
                    'id' => $category->getCategoryId(),
                    'url' => $this->url->getFullCategoryRoute($category, $storeId),
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
    public function getArticleItems(int $storeId): array
    {
        $articleItems = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $storeId)
            ->addFilter(ArticleInterface::CATEGORY_ID, $this->enabledCategoryIds, 'in')
            ->create();
        $articles = $this->articleRepository->getList($searchCriteria)
            ->getItems();
        foreach ($articles as $article) {
            $articleItems[$article->getArticleId()] = $this->getSitemapItem(
                [
                    'id' => $article->getArticleId(),
                    'url' => $this->url->getFullArticleRoute($article, $storeId),
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
    private function getChangeFreq(int $storeId): float
    {
        return $this->config->getSitemapChangeFrequency($storeId);
    }

    /**
     * Get priority
     *
     * @param int $storeId
     * @return string
     */
    private function getPriority(int $storeId): string
    {
        return $this->config->getSitemapPriority($storeId);
    }

    /**
     * Current date/time
     *
     * @return string
     */
    private function getCurrentDateTime(): string
    {
        return (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * Retrieve sitemap item 2.3.x compatibility
     *
     * @param array $itemData
     * @return SitemapItemInterface
     */
    protected function getSitemapItem(array $itemData): SitemapItemInterface
    {
        if (!$this->itemFactory) {
            $this->itemFactory = $this->objectManager->create(self::SITEMAP_ITEM_FACTORY_CLASS);
        }

        return $this->itemFactory->create($itemData);
    }
}

<?php
namespace Aheadworks\FaqFree\ViewModel\Product;

use Aheadworks\FaqFree\Model\Article\Listing\Builder as ArticleListBuilder;
use Aheadworks\FaqFree\Model\Product\Locator as ProductLocator;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Article as ArticleModel;
use Aheadworks\FaqFree\Model\Category as CategoryModel;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\FaqFree\Model\Config;

class ArticleList implements ArgumentInterface
{
    /**
     * @var ProductLocator
     */
    private $productLocator;

    /**
     * @var ArticleListBuilder
     */
    private $articleListBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ArticleInterface[]
     */
    private $activeProductArticles = [];

    /**
     * ArticleList constructor.
     * @param ProductLocator $productLocator
     * @param ArticleListBuilder $articleListBuilder
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        ProductLocator $productLocator,
        ArticleListBuilder $articleListBuilder,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->productLocator = $productLocator;
        $this->articleListBuilder = $articleListBuilder;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Returns current articles
     *
     * @return ArticleInterface[]
     */
    public function getCurrentProductArticles()
    {
        if (empty($this->activeProductArticles)) {
            try {
                $currentProduct = $this->productLocator->getProduct();
                $currentStore = $this->storeManager->getStore();

                $this->articleListBuilder
                    ->getSearchCriteriaBuilder()
                    ->addFilter(ArticleInterface::PRODUCT_IDS, $currentProduct->getId())
                    ->addFilter(ArticleInterface::STORE_IDS, $currentStore->getId())
                    ->addFilter(ArticleInterface::IS_ENABLE, true);

                $this->activeProductArticles = $this->articleListBuilder->getArticleList();
            } catch (LocalizedException $e) {
                $this->activeProductArticles = [];
            }
        }

        return $this->activeProductArticles;
    }

    /**
     * Checks if need to show faq tab on product page
     *
     * @return bool
     */
    public function isShowFaqProductTab()
    {
        return !$this->config->isDisabledFaqForCurrentCustomer() && !empty($this->getCurrentProductArticles());
    }

    /**
     * Returns block identities
     *
     * @return string[]
     */
    public function getBlockIdentities()
    {
        $identities = [];

        try {
            $currentProduct = $this->productLocator->getProduct();
            $identities = [Product::CACHE_TAG . '_' . $currentProduct->getId()];

            $this->articleListBuilder
                ->getSearchCriteriaBuilder()
                ->addFilter(ArticleInterface::PRODUCT_IDS, $currentProduct->getId());

            $allProductArticles = $this->articleListBuilder->getArticleList();
        } catch (LocalizedException $e) {
            $allProductArticles = [];
        }

        foreach ($allProductArticles as $article) {
            $identities[] = ArticleModel::CACHE_TAG . '_' . $article->getArticleId();
            $identities[] = CategoryModel::CACHE_TAG . '_' . $article->getCategoryId();
        }

        return array_unique($identities);
    }
}

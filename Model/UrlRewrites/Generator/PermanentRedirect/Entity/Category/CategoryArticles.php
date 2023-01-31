<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Entity\Category;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\AbstractGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\PathBuilder;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Aheadworks\FaqFree\Api\CategoryManagementInterface as CategoryManager;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class CategoryArticles extends AbstractGenerator
{
    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var PathBuilder
     */
    private $pathBuilder;

    /**
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var CategoryManager
     */
    private $categoryManager;

    /**
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param CategoryManager $categoryManager
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        CategoryManager $categoryManager,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->categoryManager = $categoryManager;
    }

    /**
     * Returns rewrites for entity
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @param int $storeId
     * @return UrlRewrite[]
     * @throws LocalizedException
     */
    protected function getEntityRewrites($oldEntityState, $newEntityState, $storeId)
    {
        /** @var ArticleInterface[] $articles */
        $articles = $this->categoryManager->getCategoryArticles($newEntityState, $storeId);

        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        $result = [];
        foreach ($articles as $article) {
            $requestPath = $this->pathBuilder->buildArticlePath($urlConfigMetadata, $article, $oldEntityState);
            $targetPath = $this->pathBuilder->buildArticlePath($urlConfigMetadata, $article, $newEntityState);

            if ($requestPath !== $targetPath) {
                $result[] = $this->urlRewriteFactory->create()
                    ->setRequestPath($requestPath)
                    ->setTargetPath($targetPath)
                    ->setEntityType(UrlRewriteEntityType::TYPE_ARTICLE)
                    ->setEntityId($article->getArticleId())
                    ->setStoreId($storeId)
                    ->setRedirectType(UrlRewriteOptionProvider::PERMANENT);
            }
        }

        return $result;
    }

    /**
     * Returns processed entity type
     *
     * @return string
     */
    protected function getEntityType()
    {
        return CategoryInterface::class;
    }

    /**
     * Checks if fields responsible for url, changed for entity
     *
     * @param mixed $oldEntityState
     * @param mixed $newEntityState
     * @return bool
     */
    protected function isNeedGenerateRewrites($oldEntityState, $newEntityState)
    {
        return $oldEntityState->getUrlKey() !== $newEntityState->getUrlKey();
    }
}

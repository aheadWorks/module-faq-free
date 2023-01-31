<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Entity\Article;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\AbstractGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\PathBuilder;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Aheadworks\FaqFree\Api\CategoryRepositoryInterface as CategoryRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Article extends AbstractGenerator
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
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param CategoryRepository $categoryRepository
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        CategoryRepository $categoryRepository,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->categoryRepository = $categoryRepository;
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
        /** @var CategoryInterface $oldEntityStateCategory */
        $oldEntityStateCategory = $this->categoryRepository->getById($oldEntityState->getCategoryId());
        /** @var CategoryInterface $newEntityStateCategory */
        $newEntityStateCategory = $this->categoryRepository->getById($newEntityState->getCategoryId());

        /** @var UrlConfigMetadataModel $configMetadata */
        $configMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);
        $requestPath = $this->pathBuilder->buildArticlePath($configMetadata, $oldEntityState, $oldEntityStateCategory);
        $targetPath = $this->pathBuilder->buildArticlePath($configMetadata, $newEntityState, $newEntityStateCategory);

        $result = [];
        if ($requestPath !== $targetPath) {
            $result[] = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_ARTICLE)
                ->setEntityId($newEntityState->getArticleId())
                ->setStoreId($storeId)
                ->setRedirectType(UrlRewriteOptionProvider::PERMANENT);
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
        return ArticleInterface::class;
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
        return $oldEntityState->getUrlKey() !== $newEntityState->getUrlKey()
            || $oldEntityState->getCategoryId() !== $newEntityState->getCategoryId();
    }
}

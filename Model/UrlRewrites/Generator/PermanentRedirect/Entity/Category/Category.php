<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Entity\Category;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\AbstractGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\PathBuilder;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Aheadworks\FaqFree\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Exception\LocalizedException;

class Category extends AbstractGenerator
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
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
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
        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);
        $requestPath = $this->pathBuilder->buildCategoryPath($urlConfigMetadata, $oldEntityState);
        $targetPath = $this->pathBuilder->buildCategoryPath($urlConfigMetadata, $newEntityState);

        $result = [];
        if ($requestPath !== $targetPath) {
            $result[] = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_CATEGORY)
                ->setEntityId($newEntityState->getCategoryId())
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

<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Config;

use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\AbstractGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\PathBuilder;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class FaqRoute extends AbstractGenerator
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
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param Config $config
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        Config $config,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
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
        $result = [];

        $requestPath = $this->pathBuilder->buildFaqHomePath($oldEntityState);
        $targetPath = $this->pathBuilder->buildFaqHomePath($newEntityState);

        if ($requestPath !== $targetPath) {
            $result[] = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_FAQ_ROUTE)
                ->setEntityId(1)
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
        return UrlConfigMetadataModel::class;
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
        return $oldEntityState->getFaqRoute() !== $newEntityState->getFaqRoute();
    }
}

<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Service;

use Aheadworks\FaqFree\Model\UrlRewrites\RewriteUpdater;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\Config\Composite
    as PermanentRedirectsGenerator;
use Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect\UpdatedExistingRedirectsGenerator;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;

class Config
{
    /**
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var RewriteUpdater
     */
    private $rewriteUpdater;

    /**
     * @var PermanentRedirectsGenerator
     */
    private $permanentRedirectsGenerator;

    /**
     * @var UpdatedExistingRedirectsGenerator
     */
    private $updatedExistingRedirectsGenerator;

    /**
     * @var MergeDataProviderFactory
     */
    private $mergeDataProviderFactory;

    /**
     * Config constructor.
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param RewriteUpdater $rewriteUpdater
     * @param PermanentRedirectsGenerator $permanentRedirectsGenerator
     * @param UpdatedExistingRedirectsGenerator $updatedExistingRedirectsGenerator
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     */
    public function __construct(
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        RewriteUpdater $rewriteUpdater,
        PermanentRedirectsGenerator $permanentRedirectsGenerator,
        UpdatedExistingRedirectsGenerator $updatedExistingRedirectsGenerator,
        MergeDataProviderFactory $mergeDataProviderFactory
    ) {
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->rewriteUpdater = $rewriteUpdater;
        $this->permanentRedirectsGenerator = $permanentRedirectsGenerator;
        $this->updatedExistingRedirectsGenerator = $updatedExistingRedirectsGenerator;
        $this->mergeDataProviderFactory = $mergeDataProviderFactory;
    }

    /**
     * Updates permanent redirects
     *
     * @param UrlConfigMetadataModel[]  $oldUrlConfigMetadata
     * @throws LocalizedException
     */
    public function updatePermanentRedirects($oldUrlConfigMetadata)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($oldUrlConfigMetadata as $oldMetadataItem) {
            /** @var UrlConfigMetadataModel $newConfig */
            $newMetadataItem = $this->storeUrlConfigMetadataFactory->create($oldMetadataItem->getStoreId());

            $newRedirects = $this->permanentRedirectsGenerator->generate(
                $oldMetadataItem,
                $newMetadataItem,
                $oldMetadataItem->getStoreId()
            );

            $updatedExistingRedirects = $this->updatedExistingRedirectsGenerator->generate($newRedirects);
            $mergeDataProvider->merge($newRedirects);
            $mergeDataProvider->merge($updatedExistingRedirects);
        }

        $allRedirects = $mergeDataProvider->getData();
        $this->rewriteUpdater->update($allRedirects);
    }

    /**
     * Update controller rewrites
     *
     * @param UrlConfigMetadataModel[] $oldUrlConfigMetadata
     * @return UrlConfigMetadataModel[];
     */
    public function updateControllerRewrites($oldUrlConfigMetadata)
    {
        return $oldUrlConfigMetadata;
    }
}

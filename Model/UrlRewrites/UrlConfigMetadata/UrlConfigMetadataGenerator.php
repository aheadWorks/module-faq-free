<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata;

use Aheadworks\FaqFree\Model\StoreResolver;

class UrlConfigMetadataGenerator
{
    /**
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        StoreResolver $storeResolver
    ) {
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->storeResolver = $storeResolver;
    }

    /**
     * Generates UrlConfigMetadata objects for store, website stores or all stores if store|website not defined
     *
     * @param int|null $websiteId
     * @param int|null $storeId
     * @return UrlConfigMetadata[]
     */
    public function generate($websiteId = null, $storeId = null)
    {
        if ($storeId) {
            $result = [$this->storeUrlConfigMetadataFactory->create($storeId)];
        } elseif ($websiteId) {
            $result = $this->generateForWebsite($websiteId);
        } else {
            $result = $this->generateForAllStores();
        }

        return $result;
    }

    /**
     * Generates UrlConfigMetadata objects for websites
     *
     * @param int $websiteId
     * @return UrlConfigMetadata[]
     */
    private function generateForWebsite($websiteId)
    {
        $websiteStores = $this->storeResolver->getStoreIds([$websiteId]);

        $result = [];
        foreach ($websiteStores as $store) {
            $result[] = $this->storeUrlConfigMetadataFactory->create($store);
        }

        return $result;
    }

    /**
     * Generates UrlConfigMetadata objects for all stores
     *
     * @return UrlConfigMetadata[]
     */
    private function generateForAllStores()
    {
        $stores = $this->storeResolver->getAllStoreIds();

        $result = [];
        foreach ($stores as $store) {
            $result[] = $this->storeUrlConfigMetadataFactory->create($store);
        }

        return $result;
    }
}

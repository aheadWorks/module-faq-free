<?php
namespace Aheadworks\FaqFree\Model;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreResolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreWebsiteRelationInterface
     */
    private $storeWebsiteRelation;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StoreWebsiteRelationInterface $storeWebsiteRelation
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreWebsiteRelationInterface $storeWebsiteRelation
    ) {
        $this->storeManager = $storeManager;
        $this->storeWebsiteRelation = $storeWebsiteRelation;
    }

    /**
     * Get store ids
     *
     * @param int[] $websiteIds
     * @return array
     */
    public function getStoreIds($websiteIds)
    {
        $storeIdsArrays = [];
        foreach ($websiteIds as $websiteId) {
            $storeIdsArrays[] = $this->storeWebsiteRelation->getStoreByWebsiteId($websiteId);
        }

        return array_unique(array_merge(...$storeIdsArrays));
    }

    /**
     * Get all store ids
     *
     * @return int[]
     */
    public function getAllStoreIds()
    {
        return array_keys($this->storeManager->getStores());
    }
}

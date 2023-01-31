<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Store;

use Magento\Store\Model\StoreManagerInterface;

class Resolver
{
    private const ALL_STORE_VIEWS = '0';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Resolver constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Returns store ids for rewrites generating
     *
     * @param int[] $oldEntityStateStoreIds
     * @param int[] $newEntityStateStoreIds
     * @return int[]
     */
    public function resolve($oldEntityStateStoreIds, $newEntityStateStoreIds)
    {
        if ($this->isAllStoresSelectedForBoth($oldEntityStateStoreIds, $newEntityStateStoreIds)) {
                $result = $this->getAllStoreIds();
        } elseif ($this->isStoresBecomeNarrower($oldEntityStateStoreIds, $newEntityStateStoreIds)) {
                $result = $newEntityStateStoreIds;
        } elseif ($this->isStoresBecomeWider($oldEntityStateStoreIds, $newEntityStateStoreIds)) {
            $result = $oldEntityStateStoreIds;
        } else {
            $result = array_intersect($oldEntityStateStoreIds, $newEntityStateStoreIds);
        }

        return $result;
    }

    /**
     * Is all stores selected for both
     *
     * @param int[] $oldEntityStateStoreIds
     * @param int[] $newEntityStateStoreIds
     * @return bool
     */
    private function isAllStoresSelectedForBoth($oldEntityStateStoreIds, $newEntityStateStoreIds)
    {
        return in_array(self::ALL_STORE_VIEWS, $oldEntityStateStoreIds) &&
            in_array(self::ALL_STORE_VIEWS, $newEntityStateStoreIds);
    }

    /**
     * Is stores become narrower (was all stores and become not all stores)
     *
     * @param int[] $oldEntityStateStoreIds
     * @param int[] $newEntityStateStoreIds
     * @return bool
     */
    private function isStoresBecomeNarrower($oldEntityStateStoreIds, $newEntityStateStoreIds)
    {
        return in_array(self::ALL_STORE_VIEWS, $oldEntityStateStoreIds) &&
            !in_array(self::ALL_STORE_VIEWS, $newEntityStateStoreIds);
    }

    /**
     * Is stores become wider (wasn't all stores and become all stores)
     *
     * @param int[] $oldEntityStateStoreIds
     * @param int[] $newEntityStateStoreIds
     * @return bool
     */
    private function isStoresBecomeWider($oldEntityStateStoreIds, $newEntityStateStoreIds)
    {
        return !in_array(self::ALL_STORE_VIEWS, $oldEntityStateStoreIds) &&
            in_array(self::ALL_STORE_VIEWS, $newEntityStateStoreIds);
    }

    /**
     * Returns all store ids
     *
     * @return int[]
     */
    private function getAllStoreIds()
    {
        return array_keys($this->storeManager->getStores());
    }
}

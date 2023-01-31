<?php
namespace Aheadworks\FaqFree\Model\Resolver;

use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Magento\Store\Model\Store;

class StoreIds
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve array of entity store ids
     *
     * @param ArticleInterface|CategoryInterface $entity
     * @return array
     */
    public function getStoreIds($entity)
    {
        $storeIds = [];
        if ($entity instanceof ArticleInterface
            || $entity instanceof CategoryInterface
        ) {
            $storeIds = is_array($entity->getStoreIds()) ? $entity->getStoreIds() : [];
            if (count($storeIds) == 1) {
                $storeId = reset($storeIds);
                if ($storeId == Store::DEFAULT_STORE_ID) {
                    $storeIds = array_keys($this->storeManager->getStores());
                }
            }
        }
        return $storeIds;
    }
}

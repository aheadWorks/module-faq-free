<?php
namespace Aheadworks\FaqFree\ViewModel\Search;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\FaqFree\Model\Config as FaqConfig;
use Magento\Store\Model\StoreManagerInterface;

class SearchForm implements ArgumentInterface
{
    /**
     * @var FaqConfig
     */
    private $faqConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param FaqConfig $faqConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        FaqConfig $faqConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->faqConfig = $faqConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns search query min length
     *
     * @return int
     */
    public function getMinSearchQueryLength()
    {
        return $this->faqConfig->getMinSearchQueryLength($this->getCurrentStoreId());
    }

    /**
     * Returns search query max length
     *
     * @return int
     */
    public function getMaxSearchQueryLength()
    {
        return $this->faqConfig->getMaxSearchQueryLength($this->getCurrentStoreId());
    }

    /**
     * Returns current storeId
     *
     * @return int|null
     */
    private function getCurrentStoreId()
    {
        try {
            $id = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $id = null;
        }

        return $id;
    }
}

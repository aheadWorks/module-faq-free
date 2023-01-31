<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata;

use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadataFactory;

class StoreUrlConfigMetadataFactory
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlConfigMetadataFactory
     */
    private $urlConfigMetadataFactory;

    /**
     * StoreUrlConfigMetadataFactory constructor.
     * @param Config $config
     * @param \Aheadworks\FaqFree\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadataFactory $urlConfigMetadataFactory
     */
    public function __construct(
        Config $config,
        UrlConfigMetadataFactory $urlConfigMetadataFactory
    ) {
        $this->config = $config;
        $this->urlConfigMetadataFactory = $urlConfigMetadataFactory;
    }

    /**
     * Creates UrlConfigMetadata object for store
     *
     * @param int $storeId
     * @return UrlConfigMetadata
     */
    public function create($storeId)
    {
        return $this->urlConfigMetadataFactory
            ->create()
            ->setFaqRoute($this->config->getFaqRoute($storeId))
            ->setArticleUrlSuffix($this->config->getArticleUrlSuffix($storeId))
            ->setCategoryUrlSuffix($this->config->getCategoryUrlSuffix($storeId))
            ->setStoreId($storeId);
    }
}

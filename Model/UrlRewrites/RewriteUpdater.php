<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;

class RewriteUpdater
{
    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * @param RewriteStorageInterface $rewriteStorage
     */
    public function __construct(
        RewriteStorageInterface $rewriteStorage
    ) {
        $this->rewriteStorage = $rewriteStorage;
    }

    /**
     * Update urls
     *
     * @param UrlRewrite[] $urls
     */
    public function update(array $urls)
    {
        $this->rewriteStorage->replace($urls);
    }
}

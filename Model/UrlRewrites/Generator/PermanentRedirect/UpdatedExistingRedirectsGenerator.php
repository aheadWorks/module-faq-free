<?php
namespace Aheadworks\FaqFree\Model\UrlRewrites\Generator\PermanentRedirect;

use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class responsible for generating redirects based on existing redirects, its necessary to avoid redirects chain when
 * previous redirect redirects to next and then redirects to next and ...(for example we have redirect url_1 -> url_2,
 * then we need generate redirect url_2 -> url_3 and to avoid redirects chain url_1 -> url_2 -> url_3 when try to access
 * url_1 we need to update redirect url_1 -> url_2 to url_1 -> url_3)
 */
class UpdatedExistingRedirectsGenerator
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
     * Generate redirects
     *
     * @param UrlRewrite[] $newRedirects
     */
    public function generate($newRedirects)
    {
        $result = [];

        foreach ($newRedirects as $newRedirect) {
            $existingRedirects = $this->rewriteStorage->findAllByData([
                'target_path' => $newRedirect->getRequestPath(),
                'store_id' => $newRedirect->getStoreId(),
                'redirect_type' => $newRedirect->getRedirectType()
            ]);

            /** @var  UrlRewrite $existingRedirect */
            foreach ($existingRedirects as $existingRedirect) {
                $existingRedirect->setTargetPath($newRedirect->getTargetPath());
                $result[] = $existingRedirect;
            }
        }

        return $result;
    }
}

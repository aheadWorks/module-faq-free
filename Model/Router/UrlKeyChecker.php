<?php
namespace Aheadworks\FaqFree\Model\Router;

class UrlKeyChecker
{
    /**
     * Checks if url key has incorrect slash in the end
     *
     * @param string $urlKeyWithSuffix
     * @param string $suffix
     * @return bool
     */
    public function isUrlKeyEndsWithIllegalSlash($urlKeyWithSuffix, $suffix)
    {
        $isLastSlashInUrlKey = substr($urlKeyWithSuffix, -1) === '/';
        $isNoLastSlashInSuffix = substr($suffix, -1) !== '/';

        return $isLastSlashInUrlKey && $isNoLastSlashInSuffix;
    }

    /**
     * Checks if url key doesn't has required slash in the end
     *
     * @param string $urlKeyWithSuffix
     * @param string $suffix
     * @return bool
     */
    public function isUrlKeyRequiresSlashInEnd($urlKeyWithSuffix, $suffix)
    {
        $isNoLastSlashInUrlKey = substr($urlKeyWithSuffix, -1) !== '/';
        $isLastSlashInSuffix = substr($suffix, -1) === '/';

        return $isNoLastSlashInUrlKey && $isLastSlashInSuffix;
    }
}

<?php
namespace Aheadworks\FaqFree\Model\Router;

class PathUrlKeyExtractor
{
    /**
     * Returns url key(last part of path)
     *
     * @param string $path
     * @return string
     */
    public function getUrlKey($path)
    {
        $pathWithoutEndSlash = rtrim((string)$path, '/');
        $urlKeyPosition = strrpos((string)$pathWithoutEndSlash, '/', -1);

        return ltrim(substr((string)$path, $urlKeyPosition), '/');
    }

    /**
     * Returns url key(last part of path) without suffix
     *
     * @param string $path
     * @param string $urlSuffix
     * @return string|null
     */
    public function getUrlKeyWithoutSuffix($path, $urlSuffix)
    {
        $urlSuffix = trim((string)$urlSuffix);
        $urlKeyWithSuffix = $this->getUrlKey($path);

        if ($urlSuffix) {
            $result =  strrpos($urlKeyWithSuffix, $urlSuffix)
                ? substr($urlKeyWithSuffix, 0, strrpos($urlKeyWithSuffix, $urlSuffix))
                : null;
        } else {
            $result = $urlKeyWithSuffix;
        }

        return $result;
    }
}

<?php
namespace Aheadworks\FaqFree\Model\Metadata;

use Aheadworks\FaqFree\Model\Config;

abstract class AbstractMetadata implements MetadataInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * AbstractMetadata constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Return title
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getTitle($entity = null)
    {
        $pageEntityTitle = $this->getPageEntityTitle($entity);
        $separator = $this->config->getTitleSeparator() ?: ' ';
        $prefix = $this->config->getTitlePrefix();
        $suffix = $this->config->getTitleSuffix();

        return ($prefix ? $prefix . $separator : '') . $pageEntityTitle . ($suffix ? $separator . $suffix : '');
    }

    /**
     * Return meta title
     *
     * @param mixed|null $entity
     * @return string
     */
    public function getMetaTitle($entity = null)
    {
        $pageEntityTitle = $this->getPageEntityTitle($entity);
        $suffix = trim((string)$this->config->getMetaTitleSuffix());

        return $pageEntityTitle . ($suffix ? ' ' . $suffix : '');
    }

    /**
     * Get page entity title
     *
     * @param mixed|null $entity
     * @return string
     */
    abstract protected function getPageEntityTitle($entity = null);

    /**
     * Return meta description
     *
     * @param mixed|null $entity
     * @return string
     */
    abstract public function getMetaDescription($entity = null);

    /**
     * Return meta keywords
     *
     * @param mixed|null $entity
     * @return string
     */
    abstract public function getMetaKeywords($entity = null);
}
